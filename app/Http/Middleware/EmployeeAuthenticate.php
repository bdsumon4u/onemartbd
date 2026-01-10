<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class EmployeeAuthenticate extends Middleware
{
    #[\Override]
    protected function authenticate($request = null, array $guards = [])
    {

        if ($this->auth->guard('employee')->check()) {
            return $this->auth->shouldUse('employee');
        }
        $this->unauthenticated($request, ['employee']);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    #[\Override]
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('employee.login');
        }
    }
}
