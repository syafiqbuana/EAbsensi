<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
            if (config('app.env') !== 'local') {
        URL::forceScheme('https');
    }

    // atau kalau tetap mau force meski di local:
    URL::forceRootUrl(config('app.url'));
    }
}
