<?php

namespace Dev3bdulrahman\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Machine extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'mfg_machines';

    protected $fillable = [
        'company_id',
        'work_center_id',
        'name',
        'code',
        'cost_per_hour',
        'status',
    ];

    protected $casts = [
        'cost_per_hour' => 'decimal:2',
    ];

    public function workCenter(): BelongsTo
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id');
    }
}
