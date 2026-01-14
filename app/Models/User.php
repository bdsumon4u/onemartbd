<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'phone', 'address', 'status'];

    protected $hidden = ['password', 'remember_token'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime'];
    }

    // Backward-compatible accessor
    public function get_orders(): HasMany
    {
        return $this->orders();
    }
}
