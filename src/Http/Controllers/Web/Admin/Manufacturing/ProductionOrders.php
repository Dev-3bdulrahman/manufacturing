<?php

namespace Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing;

use Dev3bdulrahman\Manufacturing\Models\ProductionOrder;
use Dev3bdulrahman\Manufacturing\Models\BillOfMaterial;
use Dev3bdulrahman\Manufacturing\Models\WorkCenter;
use Dev3bdulrahman\Manufacturing\Services\ManufacturingService;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.admin')]
class ProductionOrders extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    // Form fields for creation
    public bool $showFormModal = false;
    public ?int $bomId = null;
    public ?int $warehouseId = null;
    public float $quantity = 1.00;
    public string $startDate = '';
    
    // Routing steps
    public array $routingSteps = []; // array of ['work_center_id' => X, 'name' => Y, 'planned_hours' => Z]

    // Complete Order Modal fields
    public bool $showCompleteModal = false;
    public ?int $completeOrderId = null;
    public array $actualConsumptions = []; // product_id => qty_consumed

    protected ManufacturingService $mfgService;

    public function boot(ManufacturingService $mfgService): void
    {
        $this->mfgService = $mfgService;
    }

    public function mount(): void
    {
        $this->startDate = Carbon::today()->format('Y-m-d');
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetFields();
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->routingSteps = [];
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetFields();
    }

    public function addRoutingStep(): void
    {
        $this->routingSteps[] = [
            'work_center_id' => '',
            'name' => '',
            'planned_hours' => 1.00,
        ];
    }

    public function removeRoutingStep(int $index): void
    {
        unset($this->routingSteps[$index]);
        $this->routingSteps = array_values($this->routingSteps);
    }

    public function save(): void
    {
        $this->validate([
            'bomId' => 'required|integer',
            'warehouseId' => 'required|integer',
            'quantity' => 'required|numeric|min:0.01',
            'startDate' => 'required|date',
            'routingSteps.*.work_center_id' => 'required|integer',
            'routingSteps.*.name' => 'required|string|max:255',
            'routingSteps.*.planned_hours' => 'required|numeric|min:0.01',
        ]);

        $companyId = session('active_company_id', 1);

        $data = [
            'company_id' => $companyId,
            'bom_id' => $bomId = $this->bomId,
            'warehouse_id' => $this->warehouseId,
            'quantity' => $this->quantity,
            'start_date' => $this->startDate,
            'created_by' => auth()->id(),
            'work_orders' => $this->routingSteps,
        ];

        $this->mfgService->createProductionOrder($data);
        $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.saved_success')]);

        $this->closeFormModal();
    }

    public function updateStatus(int $id, string $status): void
    {
        $order = ProductionOrder::findOrFail($id);
        
        if (in_array($status, ['planned', 'in_progress', 'cancelled'])) {
            $order->update(['status' => $status]);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.status_updated_success')]);
        }
    }

    public function openCompleteModal(int $id): void
    {
        $this->completeOrderId = $id;
        $order = ProductionOrder::with('materialConsumptions')->findOrFail($id);

        $this->actualConsumptions = [];
        foreach ($order->materialConsumptions as $mc) {
            $this->actualConsumptions[$mc->product_id] = (float) $mc->qty_expected;
        }

        $this->showCompleteModal = true;
    }

    public function closeCompleteModal(): void
    {
        $this->showCompleteModal = false;
        $this->completeOrderId = null;
        $this->actualConsumptions = [];
    }

    public function completeOrder(): void
    {
        if (!$this->completeOrderId) {
            return;
        }

        $this->mfgService->completeProductionOrder(
            $this->completeOrderId,
            $this->actualConsumptions,
            auth()->id()
        );

        $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.production_completed_success')]);
        $this->closeCompleteModal();
    }

    public function delete(int $id): void
    {
        ProductionOrder::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.deleted_success')]);
    }

    private function resetFields(): void
    {
        $this->reset(['bomId', 'warehouseId', 'quantity', 'startDate', 'routingSteps']);
    }

    public function render()
    {
        $companyId = session('active_company_id', 1);

        $orders = ProductionOrder::with(['product', 'bom', 'warehouse', 'workOrders'])
            ->where('company_id', $companyId)
            ->where(function($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function($pq) {
                      $pq->where('name', 'like', '%' . $this->search . '%');
                  });
            });

        if ($this->statusFilter) {
            $orders->where('status', $this->statusFilter);
        }

        $orders = $orders->latest()->paginate(15);

        $boms = BillOfMaterial::where('company_id', $companyId)->where('is_active', true)->get();
        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $workCenters = WorkCenter::where('company_id', $companyId)->where('status', 'active')->get();

        // Get full details of order to complete
        $completeOrder = $this->completeOrderId 
            ? ProductionOrder::with('materialConsumptions.product')->find($this->completeOrderId) 
            : null;

        return view('mfg::livewire.admin.manufacturing.production-orders', [
            'orders' => $orders,
            'boms' => $boms,
            'allBoms' => $boms,
            'warehouses' => $warehouses,
            'workCenters' => $workCenters,
            'completeOrder' => $completeOrder,
        ])->title(__('mfg::mfg.production_orders'));
    }
}
