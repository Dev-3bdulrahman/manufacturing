<?php

namespace Dev3bdulrahman\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    protected $table = 'mfg_work_orders';

    protected $fillable = [
        'production_order_id',
        'work_center_id',
        'name',
        'sequence',
        'planned_hours',
        'actual_hours',
        'status',
    ];

    protected $casts = [
        'planned_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'sequence' => 'integer',
    ];

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function workCenter(): BelongsTo
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id');
    }
}
