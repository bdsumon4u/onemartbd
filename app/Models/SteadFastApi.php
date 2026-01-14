<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SteadFastApi extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['is_active', 'api_key', 'secret_key'];
}
