<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarryBeeApi extends Model
{
    protected $fillable = [
        'is_active', 'store_id', 'email', 'password', 'access_token'
    ];
}
