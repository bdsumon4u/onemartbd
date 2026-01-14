<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelHandover extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_no', 'customer_name', 'customer_phone', 'customer_address', 'total'];
}
