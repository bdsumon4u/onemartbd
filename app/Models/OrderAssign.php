<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAssign extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'employee_id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->select('id', 'name');
    }

    // Backward-compatible accessors
    public function get_order(): BelongsTo
    {
        return $this->order();
    }

    public function get_employee(): BelongsTo
    {
        return $this->employee();
    }
}
