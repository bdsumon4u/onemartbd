<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use App\Services\StaffUserResolver;
use Closure;
use Illuminate\Http\Request;

class EnsureAttendanceForWorkRoutes
{
    public function __construct(private StaffUserResolver $staffUserResolver) {}

    public function handle(Request $request, Closure $next)
    {
        $user = $this->staffUserResolver->resolveAuthenticatedStaffUser();

        if ($user) {
            Attendance::query()->firstOrCreate(
                [
                    'staff_type' => $user->getMorphClass(),
                    'staff_id' => (int) $user->getAuthIdentifier(),
                    'date' => now()->toDateString(),
                ],
                [
                    'check_in' => now(),
                    'status' => 'present',
                    'is_off_day' => $user->isOffDay(),
                ]
            );
        }

        return $next($request);
    }
}
