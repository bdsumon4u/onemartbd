<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $expiresAt = now()->addMinutes(3);

        if (! empty(\Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP'))) {
            $ip = \Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP');
        } elseif (! empty(\Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR'))) {
            $ip = \Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = \Illuminate\Support\Facades\Request::server('REMOTE_ADDR');
        }

        if ($ip == '::1') {
            $ip = gethostname();
        }

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            if ($user->id == 1) {
                return $next($request);
            } elseif ($user->id != 1) {
                if ($this->isWithinPanelWindow($user, 1)) {
                    Cache::put('admin-is-online-'.$user->id, true, $expiresAt);
                    Admin::query()->whereKey($user->id)->update(['last_seen' => now(), 'last_login_ip' => $ip]);
                } else {
                    $this->guard()->logout();
                    $request->session()->invalidate();

                    $request->session()->regenerateToken();

                    return $request->wantsJson()
                        ? new JsonResponse([], 204)
                        : redirect('/admin');
                }
            }
        } elseif (Auth::guard('manager')->check()) {
            $user = Auth::guard('manager')->user();
            if ($this->isWithinPanelWindow($user, 2)) {
                Cache::put('manager-is-online-'.$user->id, true, $expiresAt);
                Manager::query()->whereKey($user->id)->update(['last_seen' => now(), 'last_login_ip' => $ip]);
            } else {
                $this->guard()->logout();
                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect('/manager');
            }
        } elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            if ($this->isWithinPanelWindow($user, 3)) {
                Cache::put('employee-is-online-'.$user->id, true, $expiresAt);
                Employee::query()->whereKey($user->id)->update(['last_seen' => now(), 'last_login_ip' => $ip]);
            } else {
                $this->guard()->logout();
                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect('/employee');
            }
        }
        /*if (Auth::guard('employee')->check()) {
            Employee::where('id', Auth::guard('employee')->user()->id)->update(['last_seen' => now()]);
        }*/

        return $next($request);
    }

    protected function guard()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin');
        } elseif (Auth::guard('manager')->check()) {
            return Auth::guard('manager');
        } elseif (Auth::guard('employee')->check()) {
            return Auth::guard('employee');
        }
    }

    private function isWithinPanelWindow($staffUser, int $role): bool
    {
        $currentTime = \Illuminate\Support\Facades\Date::now()->toTimeString();

        $start = $staffUser->panel_start ?: ($staffUser->start_time ?: config('attendance.default_start_time'));
        $end = $staffUser->panel_end ?: ($staffUser->end_time ?: config('attendance.default_end_time'));

        return $start <= $currentTime && $end >= $currentTime;
    }
}
