<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteHistory extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'text', 'user_id', 'user_type'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id')->select('id', 'name');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'user_id')->select('id', 'name');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user_id')->select('id', 'name');
    }

    // Backward-compatible accessors
    public function get_admin(): BelongsTo
    {
        return $this->admin();
    }

    public function get_manager(): BelongsTo
    {
        return $this->manager();
    }

    public function get_employee(): BelongsTo
    {
        return $this->employee();
    }
}
