<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = ['courier_name', 'courier_charge_isd', 'courier_charge_osd', 'is_city', 'is_zone', 'status'];

    public function city(): HasOne
    {
        return $this->hasOne(CourierCity::class);
    }

    public function zone(): HasOne
    {
        return $this->hasOne(CourierZone::class);
    }

    // Backward-compatible accessors
    public function get_city(): HasOne
    {
        return $this->city();
    }

    public function get_zone(): HasOne
    {
        return $this->zone();
    }
}
