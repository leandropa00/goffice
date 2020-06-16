<?php

namespace App\Providers;

use App\Observers\SettingsObserver;
use App\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('REDIRECT_HTTPS')) {
            \URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);

        Setting::observe(SettingsObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
