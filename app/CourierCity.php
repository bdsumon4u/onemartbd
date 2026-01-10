<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourierCity extends Model
{
    protected $fillable = [
        'courier_id',
        'courier_name',
        'city_name',
        'status',
    ];

    public function get_courier()
    {
        return $this->hasOne(Courier::class, 'id', 'courier_id');
    }
}
