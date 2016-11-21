<?php

namespace App\Providers;

use App\Contracts\Site\SiteServiceContract;
use App\Services\Site\SiteService;
use Illuminate\Support\ServiceProvider;

class SiteServiceProvider extends ServiceProvider
{
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
