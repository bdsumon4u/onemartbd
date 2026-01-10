<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourierZone extends Model
{
    protected $fillable = [
        'courier_id',
        'courier_name',
        'city_id',
        'city_name',
        'zone_name',
        'status',
    ];
}
