<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'courier_name',
        'courier_charge_isd',
        'courier_charge_osd',
        'is_city',
        'is_zone',
        'status',
    ];

    public function get_city()
    {
        return $this->hasOne(CourierCity::class, 'courier_id', 'id');
    }

    public function get_zone()
    {
        return $this->hasOne(CourierZone::class, 'courier_id', 'id');
    }
}
