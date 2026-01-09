<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedxApi extends Model
{
    protected $fillable = [
        'access_token', 'is_active'
    ];
}
