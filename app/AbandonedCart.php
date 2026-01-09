<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    protected $fillable = [
        'user_id',
        'shipping_id',
        'customer_name',
        'customer_address',
        'customer_phone',
        'abandoned_item',
        'discount',
        'shipping_cost',
        'subtotal',
        'total',
        'note',
    ];
}
