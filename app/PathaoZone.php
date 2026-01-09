<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PathaoZone extends Model
{
    protected $fillable = ['parent_id', 'city_id', 'zone_name'];
}
