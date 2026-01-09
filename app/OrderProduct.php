<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'qty', 'price', 'attributes', 'attribute_ids','purchase_cost'
    ];

    public function get_product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->with('get_attributes')->select('id','name','sku','thumb','image','slug','price','sale_price','status','purchase_cost','packaging_cost')->with('get_thumb','get_image');
    }
    //order
    public function get_order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
