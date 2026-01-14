<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourierCity extends Model
{
    use HasFactory;

    protected $fillable = ['courier_id', 'courier_name', 'city_name', 'status'];

    public function courier(): HasOne
    {
        return $this->hasOne(Courier::class, 'id', 'courier_id');
    }

    // Backward-compatible accessor
    public function get_courier(): HasOne
    {
        return $this->courier();
    }
}
