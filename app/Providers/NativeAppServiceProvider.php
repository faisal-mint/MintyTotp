<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Windows\WindowManager;
use Illuminate\Support\Facades\Log;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // In NativePHP, the window URL should point to the Laravel app
        // NativePHP automatically starts a PHP server on port 8100
        // Always use the default URL - NativePHP handles the server internally
        Window::open('main')
            ->url('http://127.0.0.1:8100')
            ->width(420)
            ->height(700)
            ->minWidth(380)
            ->minHeight(600)
            ->resizable(true)
            ->focusable(true)
            ->title(config('nativephp.name', 'MintyOTP'))
            ->afterOpen(function () {
                // Explicitly show the window after it opens
                // This is especially important in production builds where
                // the window might open but not be visible by default
                try {
                    $windowManager = app(WindowManager::class);
                    $windowManager->show('main');
                } catch (\Exception $e) {
                    // If show() fails, the window should still be visible by default
                    // Log the error for debugging
                    Log::error('Failed to show window: ' . $e->getMessage());
                }
            });
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
