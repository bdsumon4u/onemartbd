<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedxApi extends Model
{
    protected $fillable = [
        'access_token', 'is_active',
    ];
}
