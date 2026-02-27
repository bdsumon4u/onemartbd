<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;

class AuthDeviceApprovalController extends Controller
{
    public function request()
    {
        $guard = $this->detectGuard();
        $user = auth($guard)->user();
        $deviceToken = request()->cookie('device_token');
        $device = \App\Models\AuthDevice::where('user_id', $user->id)
            ->where('user_type', $guard)
            ->where('device_token', $deviceToken)
            ->first();

        if ($device && $device->approved) {
            return redirect()->route($guard.'.home');
        }

        return view('backEnd.admin.auth.device_request', compact('device', 'guard'));
    }

    public function submit()
    {
        $guard = $this->detectGuard();
        $user = auth($guard)->user();
        $deviceToken = request()->cookie('device_token');
        $device = \App\Models\AuthDevice::where('user_id', $user->id)
            ->where('user_type', $guard)
            ->where('device_token', $deviceToken)
            ->first();
        if ($device && ! $device->approved) {
            // Optionally notify admins here
            return redirect()->back()->with('info', 'Device approval requested. Please wait for admin approval.');
        }

        return redirect()->route($guard.'.home');
    }

    public function index()
    {
        $guard = $this->detectGuard();
        $devices = \App\Models\AuthDevice::query()
            ->orderByDesc('requested_at')
            ->orderBy('approved')
            ->get();

        return view('backEnd.admin.auth.device_approvals', compact('devices', 'guard'));
    }

    public function approve($id)
    {
        $device = \App\Models\AuthDevice::findOrFail($id);
        $device->approved = true;
        $device->approved_at = now();
        $device->save();

        // Optionally notify user
        return redirect()->back()->with('success', 'Device approved.');
    }

    public function reject($id)
    {
        $device = \App\Models\AuthDevice::findOrFail($id);
        $device->delete();

        // Optionally notify user
        return redirect()->back()->with('warning', 'Device rejected and removed.');
    }

    protected function detectGuard(): string
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
