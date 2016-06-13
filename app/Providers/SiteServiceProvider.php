<?php

namespace App\Providers;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Services\Server\ServerService;
use App\Services\Server\Site\SiteService;
use Illuminate\Support\ServiceProvider;

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
