<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Employee extends Authenticatable
{
    use HasFactory;
    use HasPushSubscriptions;
    use Notifiable;

    protected $fillable = ['p_id', 'name', 'email', 'phone', 'password', 'status', 'start_time', 'end_time', 'last_seen', 'last_login_ip'];

    public function products(): HasMany
    {
        return $this->hasMany(UserProducts::class, 'user_id');
    }

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }
}
