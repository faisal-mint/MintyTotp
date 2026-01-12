<?php

use NativePHP\Electron\Commands\BuildCommand;
use NativePHP\Electron\Commands\DevCommand;

return [
    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    |
    | This is the unique identifier for your application. This should match
    | the ID you use in your Electron app configuration.
    |
    */

    'id' => env('NATIVEPHP_APP_ID', 'com.mintyotp.app'),

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This is the name that will be displayed in the application window
    | and in the system menu.
    |
    */

    'name' => env('NATIVEPHP_APP_NAME', 'MintyOTP'),

    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | This is the version of your application.
    |
    */

    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Application Description
    |--------------------------------------------------------------------------
    |
    | A brief description of your application.
    |
    */

    'description' => env('NATIVEPHP_APP_DESCRIPTION', 'A desktop TOTP authenticator app'),

    /*
    |--------------------------------------------------------------------------
    | Window Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the main application window.
    |
    */

    'window' => [
        'width' => 900,
        'height' => 700,
        'min_width' => 600,
        'min_height' => 500,
        'resizable' => true,
        'title' => env('NATIVEPHP_APP_NAME', 'MintyOTP'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Build Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the application is built for different platforms.
    |
    */

    'build' => [
        'mac' => [
            'identity' => null, // Set to your Apple Developer ID for code signing
        ],
        'windows' => [
            'certificate_file' => null, // Path to your code signing certificate
            'certificate_password' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Workers
    |--------------------------------------------------------------------------
    |
    | Configure queue workers that should be started automatically.
    | Set to empty array to disable automatic queue workers.
    |
    */

    'queue_workers' => [
        // 'default' => [
        //     'queues' => ['default'],
        //     'memory_limit' => 128,
        //     'timeout' => 60,
        //     'sleep' => 3,
        // ],
    ],

];
