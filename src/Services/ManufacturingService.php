<?php

namespace Dev3bdulrahman\Manufacturing\Services;

use Dev3bdulrahman\Manufacturing\Models\BillOfMaterial;
use Dev3bdulrahman\Manufacturing\Models\BillOfMaterialItem;
use Dev3bdulrahman\Manufacturing\Models\ProductionOrder;
use Dev3bdulrahman\Manufacturing\Models\MaterialConsumption;
use Dev3bdulrahman\Manufacturing\Models\WorkOrder;
use Dev3bdulrahman\Manufacturing\Models\WorkCenter;
use Dev3bdulrahman\Inventory\Services\StockMoveService;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManufacturingService
{
    protected StockMoveService $stockMoveService;

    public function __construct()
    {
        $this->stockMoveService = new StockMoveService();
    }

    /**
     * Create or update a Bill of Materials.
     */
    public function saveBom(array $data, ?int $bomId = null): BillOfMaterial
    {
        return DB::transaction(function () use ($data, $bomId) {
            $bomData = [
                'company_id' => $data['company_id'],
                'product_id' => $data['product_id'],
                'name'       => $data['name'],
                'code'       => $data['code'],
                'quantity'   => $data['quantity'] ?? 1.00,
                'is_active'  => $data['is_active'] ?? true,
            ];

            if ($bomId) {
                $bom = BillOfMaterial::findOrFail($bomId);
                $bom->update($bomData);
                $bom->items()->delete();
            } else {
                $bom = BillOfMaterial::create($bomData);
            }

            foreach ($data['items'] as $item) {
                BillOfMaterialItem::create([
                    'bom_id'     => $bom->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                ]);
            }

            return $bom;
        });
    }

    /**
     * Create a Production Order and pre-fill expected material consumptions.
     */
    public function createProductionOrder(array $data): ProductionOrder
    {
        return DB::transaction(function () use ($data) {
            $bom = BillOfMaterial::with('items')->findOrFail($data['bom_id']);
            $companyId = $data['company_id'] ?? 1;

            // Generate order code e.g. MFG-YYYYMMDD-XXXX
            $datePrefix = 'MFG-' . date('Ymd');
            $latestOrder = ProductionOrder::where('company_id', $companyId)
                ->where('code', 'like', $datePrefix . '-%')
                ->latest('id')
                ->first();

            $sequence = 1;
            if ($latestOrder) {
                $parts = explode('-', $latestOrder->code);
                $lastSeq = (int) end($parts);
                $sequence = $lastSeq + 1;
            }
            $code = $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $order = ProductionOrder::create([
                'company_id'   => $companyId,
                'product_id'   => $bom->product_id,
                'bom_id'       => $bom->id,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'code'         => $code,
                'quantity'     => $data['quantity'],
                'start_date'   => $data['start_date'] ?? Carbon::today(),
                'status'       => 'draft',
                'created_by'   => $data['created_by'] ?? null,
            ]);

            // Pre-fill expected consumptions based on BOM items scaled to order quantity
            $scale = $order->quantity / $bom->quantity;
            foreach ($bom->items as $bomItem) {
                MaterialConsumption::create([
                    'production_order_id' => $order->id,
                    'product_id'          => $bomItem->product_id,
                    'warehouse_id'        => $data['raw_material_warehouse_id'] ?? $data['warehouse_id'] ?? null,
                    'qty_expected'        => $bomItem->quantity * $scale,
                    'qty_consumed'        => $bomItem->quantity * $scale, // Default to expected
                ]);
            }

            // Create optional initial work orders if routing steps provided
            if (isset($data['work_orders']) && is_array($data['work_orders'])) {
                foreach ($data['work_orders'] as $idx => $step) {
                    WorkOrder::create([
                        'production_order_id' => $order->id,
                        'work_center_id'      => $step['work_center_id'],
                        'name'                => $step['name'],
                        'sequence'            => $step['sequence'] ?? ($idx + 1),
                        'planned_hours'       => $step['planned_hours'] ?? 0.00,
                        'status'              => 'pending',
                    ]);
                }
            }

            return $order;
        });
    }

    /**
     * Complete a Production Order, executing raw material deductions & finished goods stock increase.
     */
    public function completeProductionOrder(int $orderId, array $actualConsumptions = [], ?int $userId = null): ProductionOrder
    {
        return DB::transaction(function () use ($orderId, $actualConsumptions, $userId) {
            $order = ProductionOrder::with(['product', 'workOrders.workCenter', 'materialConsumptions.product'])->findOrFail($orderId);

            if ($order->status === 'completed') {
                return $order;
            }

            $totalRawMaterialsCost = 0.00;

            // 1. Process Material Consumption
            foreach ($order->materialConsumptions as $consumption) {
                $productId = $consumption->product_id;
                $actualQty = $actualConsumptions[$productId] ?? $consumption->qty_expected;

                $consumption->update([
                    'qty_consumed' => $actualQty,
                ]);

                // Calculate cost for raw materials
                $rawProduct = $consumption->product;
                $costPrice = $rawProduct ? $rawProduct->cost_price : 0.00;
                $totalRawMaterialsCost += ($actualQty * $costPrice);

                // Deduct raw material stock
                if ($order->warehouse_id && $actualQty > 0) {
                    $this->stockMoveService->logMove([
                        'company_id'   => $order->company_id,
                        'warehouse_id' => $consumption->warehouse_id ?? $order->warehouse_id,
                        'product_id'   => $productId,
                        'type'         => 'out',
                        'quantity'     => $actualQty,
                        'reference'    => $order->code,
                        'source_type'  => ProductionOrder::class,
                        'source_id'    => $order->id,
                        'created_by'   => $userId,
                    ]);
                }
            }

            // 2. Process labor & overhead cost from Work Orders
            $totalLaborCost = 0.00;
            foreach ($order->workOrders as $workOrder) {
                $workOrder->update(['status' => 'completed']);
                $wc = $workOrder->workCenter;
                if ($wc && $workOrder->planned_hours > 0) {
                    $totalLaborCost += ($workOrder->planned_hours * $wc->cost_per_hour);
                }
            }

            $totalProductionCost = $totalRawMaterialsCost + $totalLaborCost;

            // 3. Add Finished Product stock
            if ($order->warehouse_id && $order->quantity > 0) {
                $this->stockMoveService->logMove([
                    'company_id'   => $order->company_id,
                    'warehouse_id' => $order->warehouse_id,
                    'product_id'   => $order->product_id,
                    'type'         => 'in',
                    'quantity'     => $order->quantity,
                    'reference'    => $order->code,
                    'source_type'  => ProductionOrder::class,
                    'source_id'    => $order->id,
                    'created_by'   => $userId,
                ]);
            }

            // 4. Finalize Production Order status and cost
            $order->update([
                'status'   => 'completed',
                'end_date' => Carbon::now(),
                'cost'     => $totalProductionCost,
            ]);

            return $order;
        });
    }
}
