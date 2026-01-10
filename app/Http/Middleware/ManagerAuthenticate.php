<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ManagerAuthenticate extends Middleware
{

    #[\Override]
    protected function authenticate($request = null, array $guards = [])
    {

        if ($this->auth->guard('manager')->check()) {
            return $this->auth->shouldUse('manager');
        }
        $this->unauthenticated($request, ['manager']);
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
            return route('manager.login');
        }
    }
}
