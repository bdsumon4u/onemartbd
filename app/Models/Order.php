<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ip_address',
        'order_date',
        'invoice_id',
        'memo_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'courier_id',
        'courier_city_id',
        'courier_zone_id',
        'payment_method',
        'shipping_method',
        'shipping_cost',
        'courier_charge_cost',
        'discount',
        'sub_total',
        'total',
        'paid',
        'due',
        'status',
        'payment_status',
        'courier_status',
        'courier_status_reason',
        'courier_api_response',
        'courier_note',
        'staff_note',
        'pathao_consignment_id',
        'redx_tracking_id',
        'stead_fast_consignment_id',
        'carrybee_consignment_id',
        'is_fake',
        'deleted_at',
        'deleted_by',
        'customer_activity',
        'return_received_at',
        'source',
        'handover_date',
    ];

    public function get_products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id')->select('order_id', 'product_id', 'qty', 'attributes', 'attribute_ids', 'price', 'purchase_cost')->with('get_product');
    }

    public function get_courier()
    {
        return $this->hasOne(Courier::class, 'id', 'courier_id');
    }

    public function get_shipping_method()
    {
        return $this->hasOne(ShippingMethod::class, 'id', 'shipping_method');
    }

    public function get_assigned()
    {
        return $this->hasOne(OrderAssign::class, 'order_id', 'id');
    }

    public function get_transactions()
    {
        return $this->hasMany(OrderTransaction::class, 'order_id', 'id')->select('order_id', 'type', 'text', 'created_at')->orderBy('id', 'desc');
    }

    public function get_note_history()
    {
        return $this->hasMany(NoteHistory::class, 'order_id', 'id')->orderBy('id', 'desc');
    }

    public function get_customer()
    {
        return $this->hasOne(User::class, 'id', 'customer_id')->with('get_orders');
    }

    /*public function get_duplicate()
    {
        return $this->where([['customer_phone', $this->customer_phone], ['status', '!=', 1]])->count();
    }*/
}
