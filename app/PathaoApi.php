<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PathaoApi extends Model
{
    protected $fillable = [
        'is_active',
        'access_token',
        'refresh_token',
        'client_id',
        'client_secret',
        'username',
        'password',
        'store_id',
    ];
}
