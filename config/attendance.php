<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Work Schedule
    |--------------------------------------------------------------------------
    |
    | These are the default start and end times used when an employee does not
    | have a custom schedule set. Used for overtime, late fee, and auto-checkout
    | calculations across the application.
    |
    */

    'default_start_time' => env('DEFAULT_START_TIME', '10:00:00'),
    'default_end_time' => env('DEFAULT_END_TIME', '20:00:00'),

];
