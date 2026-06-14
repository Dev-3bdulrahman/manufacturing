<?php

namespace Dev3bdulrahman\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkCenter extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'mfg_work_centers';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'cost_per_hour',
        'status',
    ];

    protected $casts = [
        'cost_per_hour' => 'decimal:2',
    ];

    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class, 'work_center_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'work_center_id');
    }
}
