<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'password', 'status', 'last_seen', 'last_login_ip'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }
}
