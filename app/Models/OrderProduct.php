<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'qty', 'price', 'attributes', 'attribute_ids', 'purchase_cost'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)
            ->select('id', 'name', 'sku', 'thumb', 'image', 'slug', 'price', 'sale_price', 'status', 'purchase_cost', 'packaging_cost');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Backward-compatible accessors
    public function get_product(): BelongsTo
    {
        return $this->product();
    }

    public function get_order(): BelongsTo
    {
        return $this->order();
    }
}
