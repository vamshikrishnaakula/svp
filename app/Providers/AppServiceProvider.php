<?php

namespace App\Providers;

use Laravel\Fortify\Fortify;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //URL::forceScheme(env('FORCE_SCHEME', 'https'));

        // Using Bootstrap for pagination links
        Paginator::useBootstrap();
    }
}
