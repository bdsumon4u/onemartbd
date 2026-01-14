<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'text', 'amount', 'status'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
}
