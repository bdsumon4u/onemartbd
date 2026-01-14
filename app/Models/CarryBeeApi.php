<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarryBeeApi extends Model
{
    use HasFactory;

    protected $fillable = ['is_active', 'store_id', 'email', 'password', 'access_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
