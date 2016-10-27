<?php

namespace App\Providers;

use App\Contracts\Site\SiteServiceContract;
use App\Services\Site\SiteService;
use Illuminate\Support\ServiceProvider;

/**
 * Class SiteServiceProvider.
 */
class SiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SiteServiceContract::class,
            SiteService::class
        );
    }
}
