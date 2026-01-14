<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'order_id', 'text', 'comment', 'created_by', 'created_by_id', 'assigned_to'];
}
