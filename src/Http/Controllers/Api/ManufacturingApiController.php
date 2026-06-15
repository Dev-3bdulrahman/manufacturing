<?php

namespace Dev3bdulrahman\Manufacturing\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HasApiResponse;
use Dev3bdulrahman\Manufacturing\Http\Requests\Api\StoreWorkOrderApiRequest;
use Dev3bdulrahman\Manufacturing\Http\Requests\Api\UpdateWorkOrderApiRequest;
use Dev3bdulrahman\Manufacturing\Models\WorkOrder;
use Dev3bdulrahman\Manufacturing\Models\ProductionOrder;
use Dev3bdulrahman\Manufacturing\Models\BillOfMaterial;
use Dev3bdulrahman\Manufacturing\Services\ManufacturingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManufacturingApiController extends Controller
{
    use HasApiResponse;

    /**
     * List all work orders.
     */
    public function index(Request $request, ManufacturingService $service): JsonResponse
    {
        $this->authorize('viewAny', WorkOrder::class);

        $companyId = $request->user()->company_id;
        $perPage = (int) $request->get('per_page', 15);

        $workOrders = WorkOrder::whereHas('productionOrder', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with(['productionOrder', 'workCenter'])->latest()->paginate($perPage);

        return $this->success(
            $workOrders->items(),
            __('Work orders retrieved successfully'),
            200,
            [
                'current_page' => $workOrders->currentPage(),
                'last_page' => $workOrders->lastPage(),
                'per_page' => $workOrders->perPage(),
                'total' => $workOrders->total(),
            ]
        );
    }

    /**
     * Store a new work order.
     */
    public function store(StoreWorkOrderApiRequest $request, ManufacturingService $service): JsonResponse
    {
        $this->authorize('create', WorkOrder::class);

        $validated = $request->validated();
        $validated['company_id'] = $request->user()->company_id;
        $validated['created_by'] = $request->user()->id;

        $order = $service->createProductionOrder($validated);

        return $this->success(
            $order,
            __('Work order created successfully'),
            201
        );
    }

    /**
     * Show a single work order.
     */
    public function show(WorkOrder $workOrder): JsonResponse
    {
        $this->authorize('view', $workOrder);

        $workOrder->load(['productionOrder', 'workCenter']);

        return $this->success(
            $workOrder,
            __('Work order details retrieved')
        );
    }

    /**
     * Update an existing work order.
     */
    public function update(UpdateWorkOrderApiRequest $request, WorkOrder $workOrder, ManufacturingService $service): JsonResponse
    {
        $this->authorize('update', $workOrder);

        $validated = $request->validated();
        $workOrder->update($validated);

        return $this->success(
            $workOrder->fresh(),
            __('Work order updated successfully')
        );
    }

    /**
     * Delete a work order.
     */
    public function destroy(WorkOrder $workOrder): JsonResponse
    {
        $this->authorize('delete', $workOrder);

        $workOrder->delete();

        return $this->success(
            null,
            __('Work order deleted successfully')
        );
    }

    /**
     * Get list of production orders.
     */
    public function getProductionOrders(Request $request): JsonResponse
    {
        $this->authorize('viewAny', WorkOrder::class);

        $companyId = $request->user()->company_id;
        $perPage = (int) $request->get('per_page', 15);

        $orders = ProductionOrder::with(['product', 'bom', 'warehouse'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate($perPage);

        return $this->success(
            $orders->items(),
            __('Production orders retrieved successfully'),
            200,
            [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        );
    }

    /**
     * Get list of Bills of Materials.
     */
    public function getBoms(Request $request): JsonResponse
    {
        $this->authorize('viewAny', WorkOrder::class);

        $companyId = $request->user()->company_id;

        $boms = BillOfMaterial::with(['product', 'items.product'])
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        return $this->success(
            $boms,
            __('Bills of materials retrieved successfully')
        );
    }
}
