<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ip_address', 'order_date', 'invoice_id', 'memo_number', 'customer_id', 'customer_name',
        'customer_phone', 'customer_email', 'customer_address', 'courier_id', 'courier_city_id',
        'courier_zone_id', 'payment_method', 'shipping_method', 'shipping_cost', 'courier_charge_cost',
        'discount', 'sub_total', 'total', 'paid', 'due', 'status', 'payment_status', 'courier_status',
        'courier_status_reason', 'courier_api_response', 'courier_note', 'staff_note',
        'pathao_consignment_id', 'redx_tracking_id', 'stead_fast_consignment_id', 'carrybee_consignment_id',
        'is_fake', 'deleted_at', 'deleted_by', 'customer_activity', 'return_received_at', 'source', 'handover_date',
    ];

    protected function casts(): array
    {
        return [
            'handover_date' => 'datetime',
            'return_received_at' => 'datetime',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class)
            ->select('order_id', 'product_id', 'qty', 'attributes', 'attribute_ids', 'price', 'purchase_cost');
    }

    public function courier(): HasOne
    {
        return $this->hasOne(Courier::class, 'id', 'courier_id');
    }

    public function shippingMethod(): HasOne
    {
        return $this->hasOne(ShippingMethod::class, 'id', 'shipping_method');
    }

    public function assigned(): HasOne
    {
        return $this->hasOne(OrderAssign::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(OrderTransaction::class)
            ->select('order_id', 'type', 'text', 'created_at')
            ->orderBy('id', 'desc');
    }

    public function noteHistory(): HasMany
    {
        return $this->hasMany(NoteHistory::class)->orderBy('id', 'desc');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function courierCity(): BelongsTo
    {
        return $this->belongsTo(CourierCity::class, 'courier_city_id');
    }

    public function courierZone(): BelongsTo
    {
        return $this->belongsTo(CourierZone::class, 'courier_zone_id');
    }

    // Backward-compatible accessors
    public function get_products(): HasMany
    {
        return $this->products();
    }

    public function get_courier(): HasOne
    {
        return $this->courier();
    }

    public function get_shipping_method(): HasOne
    {
        return $this->shippingMethod();
    }

    public function get_assigned(): HasOne
    {
        return $this->assigned();
    }

    public function get_transactions(): HasMany
    {
        return $this->transactions();
    }

    public function get_courier_city(): BelongsTo
    {
        return $this->courierCity();
    }

    public function get_courier_zone(): BelongsTo
    {
        return $this->courierZone();
    }

    public function get_note_history(): HasMany
    {
        return $this->noteHistory();
    }

    public function get_customer(): HasOne
    {
        return $this->customer();
    }
}
