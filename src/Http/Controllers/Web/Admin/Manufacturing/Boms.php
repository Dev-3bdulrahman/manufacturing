<?php

namespace Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing;

use Dev3bdulrahman\Manufacturing\Models\BillOfMaterial;
use Dev3bdulrahman\Manufacturing\Services\ManufacturingService;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Boms extends Component
{
    use WithPagination;

    public string $search = '';

    // Form fields
    public bool $showFormModal = false;
    public ?int $bomId = null;
    public ?int $productId = null;
    public string $name = '';
    public string $code = '';
    public float $quantity = 1.00;
    public bool $isActive = true;

    // BOM items list
    public array $items = []; // array of ['product_id' => X, 'quantity' => Y]

    protected ManufacturingService $mfgService;

    public function boot(ManufacturingService $mfgService): void
    {
        $this->mfgService = $mfgService;
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetFields();
        $this->items = [
            ['product_id' => '', 'quantity' => 1.0000]
        ];
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $this->resetFields();

        $bom = BillOfMaterial::with('items')->findOrFail($id);
        $this->bomId = $bom->id;
        $this->productId = $bom->product_id;
        $this->name = $bom->name;
        $this->code = $bom->code;
        $this->quantity = (float) $bom->quantity;
        $this->isActive = (bool) $bom->is_active;

        $this->items = [];
        foreach ($bom->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'quantity' => (float) $item->quantity,
            ];
        }

        if (empty($this->items)) {
            $this->items[] = ['product_id' => '', 'quantity' => 1.0000];
        }

        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetFields();
    }

    public function addMaterial(): void
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1.0000
        ];
    }

    public function removeMaterial(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        
        if (empty($this->items)) {
            $this->items[] = ['product_id' => '', 'quantity' => 1.0000];
        }
    }

    public function save(): void
    {
        $this->validate([
            'productId' => 'required|integer',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0.01',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        $companyId = session('active_company_id', 1);

        $data = [
            'company_id' => $companyId,
            'product_id' => $this->productId,
            'name' => $this->name,
            'code' => $this->code,
            'quantity' => $this->quantity,
            'is_active' => $this->isActive,
            'items' => $this->items,
        ];

        $this->mfgService->saveBom($data, $this->bomId);
        $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.saved_success')]);

        $this->closeFormModal();
    }

    public function delete(int $id): void
    {
        BillOfMaterial::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.deleted_success')]);
    }

    private function resetFields(): void
    {
        $this->reset(['bomId', 'productId', 'name', 'code', 'quantity', 'isActive', 'items']);
    }

    public function render()
    {
        $companyId = session('active_company_id', 1);

        $boms = BillOfMaterial::with(['product', 'items.product'])
            ->where('company_id', $companyId)
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->paginate(15);

        // Fetch finished products for the main selection (products that are stockable)
        $finishedProducts = Product::where('company_id', $companyId)
            ->where('type', Product::TYPE_STOCK)
            ->get();

        // Fetch raw materials/components products for item selection
        $rawMaterials = Product::where('company_id', $companyId)
            ->whereIn('type', [Product::TYPE_RAW_MATERIAL, Product::TYPE_STOCK])
            ->get();

        return view('mfg::livewire.admin.manufacturing.boms', [
            'boms' => $boms,
            'finishedProducts' => $finishedProducts,
            'rawMaterials' => $rawMaterials,
        ])->title(__('mfg::mfg.boms'));
    }
}
