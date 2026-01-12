<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Native\Laravel\Commands\LoadStartupConfigurationCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure SQLite database file exists
        $databasePath = database_path('database.sqlite');
        if (!file_exists($databasePath)) {
            touch($databasePath);
        }

        // Register native:config command so it's always available
        if ($this->app->runningInConsole()) {
            $this->commands([
                LoadStartupConfigurationCommand::class,
            ]);
        }
    }
}
