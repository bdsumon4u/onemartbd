<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'attribute_id',
    ];

    public function get_attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    public function get_attribute_items()
    {
        return $this->hasMany(ProductAttributeItem::class, 'product_attribute_id', 'id')->with('get_attribute_item');
    }
}
