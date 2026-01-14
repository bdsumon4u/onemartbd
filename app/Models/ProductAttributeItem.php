<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['product_attribute_id', 'product_attribute_item_id'];

    public function attributeItem(): BelongsTo
    {
        return $this->belongsTo(AttributeItem::class, 'product_attribute_item_id');
    }

    // Backward-compatible accessor
    public function get_attribute_item(): BelongsTo
    {
        return $this->attributeItem();
    }
}
