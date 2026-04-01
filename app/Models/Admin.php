<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Admin extends Authenticatable
{
    use HasFactory;
    use HasPushSubscriptions;
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'status', 'start_time', 'end_time', 'last_seen', 'last_login_ip'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
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
