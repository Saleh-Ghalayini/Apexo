<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\DataAccessService::class, function ($app) {
            return new \App\Services\DataAccessService();
        });
        $this->app->singleton(\App\Services\AIToolsService::class, function ($app) {
            return new \App\Services\AIToolsService($app->make(\App\Services\DataAccessService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
