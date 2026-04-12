<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_id',
        'employee_id',
        'customer_name',
        'customer_address',
        'customer_phone',
        'abandoned_item',
        'discount',
        'shipping_cost',
        'subtotal',
        'total',
        'note',
        'status',
        'master_id',
        'slave_id',
        'slave_domain',
        'forwarding_status',
        'forwarding_last_error',
        'ip_address',
        'utm_source',
        'source',
    ];

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
