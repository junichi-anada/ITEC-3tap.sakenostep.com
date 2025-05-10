<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration for feature flags.
    | You can define flags to easily toggle features on and off.
    | Values should typically be boolean and can be controlled by environment
    | variables for flexibility across different environments.
    |
    */

    'enable_line_notification' => env('ENABLE_LINE_NOTIFICATION_ON_CART_ADD', false),

];
