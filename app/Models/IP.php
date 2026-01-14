<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IP extends Model
{
    use HasFactory;

    protected $fillable = ['ip_address', 'status'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'ip_address', 'ip_address');
    }

    // Backward-compatible accessor
    public function get_orders(): HasMany
    {
        return $this->orders();
    }
}
