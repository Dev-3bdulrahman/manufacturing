<?php

namespace Dev3bdulrahman\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;
use App\Models\Product;
use App\Models\User;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'mfg_production_orders';

    protected $fillable = [
        'company_id',
        'product_id',
        'bom_id',
        'warehouse_id',
        'code',
        'quantity',
        'start_date',
        'end_date',
        'status',
        'cost',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class, 'bom_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'production_order_id');
    }

    public function materialConsumptions(): HasMany
    {
        return $this->hasMany(MaterialConsumption::class, 'production_order_id');
    }
}
