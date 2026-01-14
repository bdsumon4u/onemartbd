<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['product_id', 'attribute_id'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductAttributeItem::class);
    }

    // Backward-compatible accessors
    public function get_attribute(): BelongsTo
    {
        return $this->attribute();
    }

    public function get_attribute_items(): HasMany
    {
        return $this->items();
    }
}
