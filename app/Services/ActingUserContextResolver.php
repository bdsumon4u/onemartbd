<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class ActingUserContextResolver
{
    /**
     * @return array{0: Authenticatable|null, 1: string|null}
     */
    public function resolve(): array
    {
        if (Auth::guard('admin')->check()) {
            return [Auth::guard('admin')->user(), 'admin'];
        }

        if (Auth::guard('manager')->check()) {
            return [Auth::guard('manager')->user(), 'manager'];
        }

        if (Auth::guard('employee')->check()) {
            return [Auth::guard('employee')->user(), 'employee'];
        }

        return [null, null];
    }
}
