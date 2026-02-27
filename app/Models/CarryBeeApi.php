<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarryBeeApi extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'store_id',
        'client_id',
        'client_secret',
        'client_context',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
