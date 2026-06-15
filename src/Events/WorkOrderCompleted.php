<?php

namespace Dev3bdulrahman\Manufacturing\Events;

use Dev3bdulrahman\Manufacturing\Models\WorkOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkOrder $workOrder,
        public int $userId,
        public int $companyId,
    ) {}
}
