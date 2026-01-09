<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SteadFastApi extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'is_active',
        'api_key',
        'secret_key',
    ];
}
