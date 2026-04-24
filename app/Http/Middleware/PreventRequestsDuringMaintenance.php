<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        'admin-settings-site-update',
        'admin-settings-site-update/check',
        'admin-settings-site-update/run',
        'admin-settings-site-update/status',
    ];
}
