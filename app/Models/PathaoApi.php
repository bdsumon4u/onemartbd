<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathaoApi extends Model
{
    use HasFactory;

    protected $fillable = ['is_active', 'access_token', 'refresh_token', 'client_id', 'client_secret', 'username', 'password', 'store_id'];
}
