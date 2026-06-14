<?php

namespace Dev3bdulrahman\Manufacturing\Http\Controllers\Web\Admin\Manufacturing;

use Dev3bdulrahman\Manufacturing\Models\WorkCenter;
use Dev3bdulrahman\Manufacturing\Models\Machine;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class WorkCenters extends Component
{
    use WithPagination;

    public string $search = '';
    public string $machineSearch = '';
    
    // Work Center Form Fields
    public bool $showWcModal = false;
    public ?int $wcId = null;
    public string $wcName = '';
    public string $wcCode = '';
    public string $wcDescription = '';
    public float $wcCostPerHour = 0.00;
    public string $wcStatus = 'active';

    // Machine Form Fields
    public bool $showMachineModal = false;
    public ?int $machineId = null;
    public string $machineName = '';
    public string $machineCode = '';
    public float $machineCostPerHour = 0.00;
    public string $machineStatus = 'active';
    public ?int $machineWcId = null;

    protected function rules(): array
    {
        return [
            'wcName' => 'required|string|max:255',
            'wcCode' => 'required|string|max:50',
            'wcCostPerHour' => 'required|numeric|min:0',
            'wcDescription' => 'nullable|string',
            'wcStatus' => 'required|in:active,inactive',
        ];
    }

    public function openWcModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->resetFields();

        if ($id) {
            $wc = WorkCenter::findOrFail($id);
            $this->wcId = $wc->id;
            $this->wcName = $wc->name;
            $this->wcCode = $wc->code;
            $this->wcDescription = $wc->description ?? '';
            $this->wcCostPerHour = (float) $wc->cost_per_hour;
            $this->wcStatus = $wc->status;
        }

        $this->showWcModal = true;
    }

    public function closeWcModal(): void
    {
        $this->showWcModal = false;
        $this->resetFields();
    }

    public function saveWc(): void
    {
        $this->validate([
            'wcName' => 'required|string|max:255',
            'wcCode' => 'required|string|max:50',
            'wcCostPerHour' => 'required|numeric|min:0',
            'wcDescription' => 'nullable|string',
            'wcStatus' => 'required|in:active,inactive',
        ]);

        $companyId = session('active_company_id', 1);

        $data = [
            'company_id' => $companyId,
            'name' => $this->wcName,
            'code' => $this->wcCode,
            'description' => $this->wcDescription,
            'cost_per_hour' => $this->wcCostPerHour,
            'status' => $this->wcStatus,
        ];

        if ($this->wcId) {
            WorkCenter::findOrFail($this->wcId)->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.saved_success')]);
        } else {
            WorkCenter::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.saved_success')]);
        }

        $this->closeWcModal();
    }

    public function deleteWc(int $id): void
    {
        WorkCenter::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.deleted_success')]);
    }

    // Machine CRUD functions
    public function openMachineModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->resetFields();

        if ($id) {
            $machine = Machine::findOrFail($id);
            $this->machineId = $machine->id;
            $this->machineName = $machine->name;
            $this->machineCode = $machine->code;
            $this->machineCostPerHour = (float) $machine->cost_per_hour;
            $this->machineStatus = $machine->status;
            $this->machineWcId = $machine->work_center_id;
        }

        $this->showMachineModal = true;
    }

    public function closeMachineModal(): void
    {
        $this->showMachineModal = false;
        $this->resetFields();
    }

    public function saveMachine(): void
    {
        $this->validate([
            'machineName' => 'required|string|max:255',
            'machineCode' => 'required|string|max:50',
            'machineCostPerHour' => 'required|numeric|min:0',
            'machineStatus' => 'required|in:active,inactive',
            'machineWcId' => 'nullable|integer',
        ]);

        $companyId = session('active_company_id', 1);

        $data = [
            'company_id' => $companyId,
            'work_center_id' => $this->machineWcId,
            'name' => $this->machineName,
            'code' => $this->machineCode,
            'cost_per_hour' => $this->machineCostPerHour,
            'status' => $this->machineStatus,
        ];

        if ($this->machineId) {
            Machine::findOrFail($this->machineId)->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.saved_success')]);
        } else {
            Machine::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.saved_success')]);
        }

        $this->closeMachineModal();
    }

    public function deleteMachine(int $id): void
    {
        Machine::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('mfg::mfg.deleted_success')]);
    }

    private function resetFields(): void
    {
        $this->reset([
            'wcId', 'wcName', 'wcCode', 'wcDescription', 'wcCostPerHour', 'wcStatus',
            'machineId', 'machineName', 'machineCode', 'machineCostPerHour', 'machineStatus', 'machineWcId'
        ]);
    }

    public function render()
    {
        $companyId = session('active_company_id', 1);

        $workCenters = WorkCenter::where('company_id', $companyId)
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->paginate(10, ['*'], 'wcPage');

        $machines = Machine::with('workCenter')
            ->where('company_id', $companyId)
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->machineSearch . '%')
                  ->orWhere('code', 'like', '%' . $this->machineSearch . '%');
            })
            ->paginate(10, ['*'], 'mPage');

        $allWorkCenters = WorkCenter::where('company_id', $companyId)->get();

        return view('mfg::livewire.admin.manufacturing.work-centers', [
            'workCenters' => $workCenters,
            'machines' => $machines,
            'allWorkCenters' => $allWorkCenters,
        ])->title(__('mfg::mfg.work_centers'));
    }
}
