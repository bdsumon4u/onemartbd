<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'last_seen',
        'last_login_ip',
    ];
}
