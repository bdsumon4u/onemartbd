<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrustedDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = $this->detectGuard($request);
        $user = auth($guard)->user();
        if (! $user) {
            return $next($request);
        }

        $deviceToken = $request->cookie('device_token');
        $userAgent = substr($request->userAgent(), 0, 255);
        $ip = $request->ip();

        // If no device token, generate and set cookie
        if (! $deviceToken) {
            $deviceToken = bin2hex(random_bytes(32));

            return redirect($request->url())
                ->cookie('device_token', $deviceToken, 60 * 24 * 365); // 1 year
        }

        $device = \App\Models\AuthDevice::where('user_id', $user->id)
            ->where('user_type', $guard)
            ->where('device_token', $deviceToken)
            ->first();

        if (! $device) {
            // Bootstrap: auto-approve first admin device
            if ($guard === 'admin' && \App\Models\AuthDevice::where('user_type', 'admin')->count() === 0) {
                \App\Models\AuthDevice::create([
                    'user_id' => $user->id,
                    'user_type' => $guard,
                    'device_token' => $deviceToken,
                    'user_agent' => $userAgent,
                    'approved' => true,
                    'requested_at' => now(),
                    'approved_at' => now(),
                    'ip_address' => $ip,
                ]);

                return $next($request);
            }
            // Create device request
            \App\Models\AuthDevice::create([
                'user_id' => $user->id,
                'user_type' => $guard,
                'device_token' => $deviceToken,
                'user_agent' => $userAgent,
                'approved' => false,
                'requested_at' => now(),
                'ip_address' => $ip,
            ]);

            return redirect()->route($guard.'.device.request');
        }

        if (! $device->approved) {
            return redirect()->route($guard.'.device.request');
        }

        // Update last seen
        $device->touch();

        return $next($request);
    }

    protected function detectGuard(Request $request): string
    {
        if (auth('admin')->check()) {
            return 'admin';
        }
        if (auth('manager')->check()) {
            return 'manager';
        }
        if (auth('employee')->check()) {
            return 'employee';
        }

        return 'web';
    }
}
