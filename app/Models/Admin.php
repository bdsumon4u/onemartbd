<?php

namespace App\Models;

use App\Enums\RoleType;
use App\Models\Concerns\HasStaffPayrollProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Admin extends Authenticatable
{
    use HasFactory, HasPushSubscriptions, HasStaffPayrollProfile, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'start_time',
        'end_time',
        'panel_start',
        'panel_end',
        'order_start',
        'order_end',
        'monthly_salary',
        'off_days',
        'last_seen',
        'last_login_ip',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'monthly_salary' => 'decimal:2',
        ];
    }

    public function getRoleAttribute(): int
    {
        return RoleType::Admin->value;
    }

    public function getStartTimeAttribute($value)
    {
        return $value ? date('H:i:s', strtotime($value)) : config('attendance.default_start_time');
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? date('H:i:s', strtotime($value)) : config('attendance.default_end_time');
    }

    public function getPanelStartAttribute($value)
    {
        return $value ? date('H:i:s', strtotime($value)) : '00:00:00';
    }

    public function getPanelEndAttribute($value)
    {
        return $value ? date('H:i:s', strtotime($value)) : '23:59:59';
    }

    public function getOrderStartAttribute($value)
    {
        return $value ? date('H:i:s', strtotime($value)) : config('attendance.default_start_time');
    }

    public function getOrderEndAttribute($value)
    {
        return $value ? date('H:i:s', strtotime($value)) : config('attendance.default_end_time');
    }
}
