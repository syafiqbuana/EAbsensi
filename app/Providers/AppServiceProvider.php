<?php

namespace App\Providers;

use App\Models\TpqProfile;
use App\Observers\TpqProfileObserver;
use App\Support\CurrentTpq;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;

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
        URL::forceRootUrl(config('app.url'));
        TpqProfile::observe(TpqProfileObserver::class);

        Event::listen(function (Logout $event) {
            CurrentTpq::clear();
        });
    }
}
