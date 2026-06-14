<?php

namespace Dev3bdulrahman\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialConsumption extends Model
{
    protected $table = 'mfg_material_consumptions';

    protected $fillable = [
        'production_order_id',
        'product_id',
        'warehouse_id',
        'qty_expected',
        'qty_consumed',
    ];

    protected $casts = [
        'qty_expected' => 'decimal:4',
        'qty_consumed' => 'decimal:4',
    ];

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
