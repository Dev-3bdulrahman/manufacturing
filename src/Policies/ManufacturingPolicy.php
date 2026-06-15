<?php

namespace Dev3bdulrahman\Manufacturing\Policies;

use App\Models\User;
use Dev3bdulrahman\Manufacturing\Models\WorkOrder;

class ManufacturingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manufacturing.work_orders.view');
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('manufacturing.work_orders.view')
            && $workOrder->productionOrder
            && $workOrder->productionOrder->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('manufacturing.work_orders.create');
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('manufacturing.work_orders.update')
            && $workOrder->productionOrder
            && $workOrder->productionOrder->company_id === $user->company_id;
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('manufacturing.work_orders.delete')
            && $workOrder->productionOrder
            && $workOrder->productionOrder->company_id === $user->company_id;
    }

    public function approve(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('manufacturing.work_orders.approve')
            && $workOrder->productionOrder
            && $workOrder->productionOrder->company_id === $user->company_id;
    }
}
