<?php

namespace Dev3bdulrahman\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillOfMaterial extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'mfg_boms';

    protected $fillable = [
        'company_id',
        'product_id',
        'name',
        'code',
        'quantity',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillOfMaterialItem::class, 'bom_id');
    }
}
