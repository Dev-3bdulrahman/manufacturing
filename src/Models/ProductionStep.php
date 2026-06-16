<?php

namespace Dev3bdulrahman\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionStep extends Model
{
    protected $table = 'mfg_production_steps';

    protected $fillable = [
        'production_order_id',
        'name',
        'description',
        'sort_order',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }
}
