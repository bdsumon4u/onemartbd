<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierZone extends Model
{
    use HasFactory;

    protected $fillable = ['courier_id', 'courier_name', 'city_id', 'city_name', 'zone_name', 'status'];
}
