<?php

namespace App\Http\Middleware;

use App\Employee;
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
                if ($user->start_time <= \Illuminate\Support\Facades\Date::now()->toTimeString() && $user->end_time >= \Illuminate\Support\Facades\Date::now()->toTimeString()) {
                    Cache::put('admin-is-online-'.$user->id, true, $expiresAt);
                    $user->update(['last_seen' => now(), 'last_login_ip' => $ip]);
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
            if ($user->start_time <= \Illuminate\Support\Facades\Date::now()->toTimeString() && $user->end_time >= \Illuminate\Support\Facades\Date::now()->toTimeString()) {
                Cache::put('manager-is-online-'.$user->id, true, $expiresAt);
                $user->update(['last_seen' => now(), 'last_login_ip' => $ip]);
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
            if ($user->start_time <= \Illuminate\Support\Facades\Date::now()->toTimeString() && $user->end_time >= \Illuminate\Support\Facades\Date::now()->toTimeString()) {
                Cache::put('employee-is-online-'.$user->id, true, $expiresAt);
                $user->update(['last_seen' => now(), 'last_login_ip' => $ip]);
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
}
