<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_attribute_id', 'product_attribute_item_id',
    ];

    public function get_attribute_item()
    {
        return $this->hasOne(AttributeItem::class, 'id', 'product_attribute_item_id');
    }
}
