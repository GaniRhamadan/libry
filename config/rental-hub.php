<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configurations
    |--------------------------------------------------------------------------
    |
    | Prefix for all package web routes and route names.
    | Default URL prefix: /rental
    | Default route name prefix: rental.
    |
    */
    'route_prefix' => env('RENTAL_ROUTE_PREFIX', 'rental'),

    'route_name_prefix' => env('RENTAL_ROUTE_NAME_PREFIX', 'rental.'),

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Database Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all rental tables to avoid naming collisions with
    | existing host application tables.
    |
    */
    'table_prefix' => env('RENTAL_TABLE_PREFIX', 'rental_'),

    /*
    |--------------------------------------------------------------------------
    | Host User Model
    |--------------------------------------------------------------------------
    |
    | The User Eloquent model of the host application.
    |
    */
    'user_model' => env('RENTAL_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Frontend CDN Assets
    |--------------------------------------------------------------------------
    |
    | When true, layout loads Tailwind CSS and Alpine.js via public CDN.
    | Set to false if you host assets locally or bundle via Vite.
    |
    */
    'use_cdn' => (bool) env('RENTAL_USE_CDN', true),

    /*
    |--------------------------------------------------------------------------
    | Booking Constraints & Fines
    |--------------------------------------------------------------------------
    |
    | Maximum allowed duration in days for a single booking reservation.
    | Default late return fee per hour (in Rupiah) if not overridden on the unit.
    |
    */
    'max_booking_days' => (int) env('RENTAL_MAX_BOOKING_DAYS', 30),

    'late_fee_per_hour' => (int) env('RENTAL_LATE_FEE_PER_HOUR', 50000),

    /*
    |--------------------------------------------------------------------------
    | Localization & Formatting
    |--------------------------------------------------------------------------
    |
    | Currency display code, symbol, and default date format.
    |
    */
    'currency' => 'IDR',

    'currency_symbol' => 'Rp ',

    'date_format' => 'd M Y H:i',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Disk used for storing uploaded vehicle photos.
    |
    */
    'disk' => env('RENTAL_DISK', 'public'),
];
