<?php

namespace Dev3bdulrahman\Manufacturing\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dev3bdulrahman\Manufacturing\Models\ProductionOrder;
use Dev3bdulrahman\Manufacturing\Models\BillOfMaterial;
use Dev3bdulrahman\Manufacturing\Services\ManufacturingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ManufacturingApiController extends Controller
{
    protected ManufacturingService $mfgService;

    public function __construct(ManufacturingService $mfgService)
    {
        $this->mfgService = $mfgService;
    }

    /**
     * Get list of production orders.
     */
    public function getProductionOrders(Request $request): JsonResponse
    {
        $companyId = $request->user()?->company_id ?? 1;

        $orders = ProductionOrder::with(['product', 'bom', 'warehouse'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Create a production order via API.
     */
    public function createProductionOrder(Request $request): JsonResponse
    {
        $request->validate([
            'bom_id' => 'required|integer|exists:mfg_boms,id',
            'warehouse_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0.01',
            'start_date' => 'nullable|date',
        ]);

        $companyId = $request->user()?->company_id ?? 1;

        $data = [
            'company_id' => $companyId,
            'bom_id' => $request->input('bom_id'),
            'warehouse_id' => $request->input('warehouse_id'),
            'quantity' => $request->input('quantity'),
            'start_date' => $request->input('start_date'),
            'created_by' => $request->user()?->id,
        ];

        $order = $this->mfgService->createProductionOrder($data);

        return response()->json([
            'success' => true,
            'message' => 'Production order created successfully',
            'data' => $order,
        ], 201);
    }

    /**
     * Get list of Bills of Materials.
     */
    public function getBoms(Request $request): JsonResponse
    {
        $companyId = $request->user()?->company_id ?? 1;

        $boms = BillOfMaterial::with(['product', 'items.product'])
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $boms,
        ]);
    }
}
