<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'status', 'start_time', 'end_time', 'last_seen', 'last_login_ip'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }
}
