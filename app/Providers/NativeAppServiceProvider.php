<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // In NativePHP, the window URL should point to the Laravel app
        // NativePHP automatically starts a PHP server and the window should load it
        // Use url('/') which will resolve to the correct URL based on the current request
        // or default to localhost:8100 (NativePHP's typical default port)
        $url = request()->getSchemeAndHttpHost() ?: 'http://127.0.0.1:8100';
        
        Window::open()
            ->url($url);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
