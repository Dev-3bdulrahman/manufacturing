<?php

namespace Dev3bdulrahman\Manufacturing\Listeners;

use App\Services\AuditLogService;
use Dev3bdulrahman\Manufacturing\Events\WorkOrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogWorkOrderCompleted implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private AuditLogService $auditLogService,
    ) {}

    /**
     * Handle the WorkOrderCompleted event.
     */
    public function handle(WorkOrderCompleted $event): void
    {
        try {
            $this->auditLogService->log(
                action: 'work_order_completed',
                companyId: $event->companyId,
                userId: $event->userId,
                model: $event->workOrder,
                oldValues: null,
                newValues: [
                    'work_order_id' => $event->workOrder->id,
                    'name' => $event->workOrder->name,
                    'production_order_id' => $event->workOrder->production_order_id,
                    'status' => $event->workOrder->status,
                ],
            );
        } catch (\Throwable $e) {
            Log::error('LogWorkOrderCompleted: Failed to log work order completion.', [
                'error' => $e->getMessage(),
                'work_order_id' => $event->workOrder->id ?? null,
                'user_id' => $event->userId ?? null,
            ]);
        }
    }
}
