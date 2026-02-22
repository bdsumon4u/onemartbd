<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthDevice extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'device_token',
        'user_agent',
        'approved',
        'requested_at',
        'approved_at',
        'ip_address',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo(null, 'user_type', 'user_id');
    }
}
