<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IP extends Model
{
    protected $fillable = [
        'ip_address', 'status',
    ];

    public function get_orders()
    {
        return $this->hasMany(Order::class, 'ip_address', 'ip_address');
    }
}
