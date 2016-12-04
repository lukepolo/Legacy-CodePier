<?php

namespace App\Providers;

use App\Services\Site\SiteService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Site\SiteServiceContract;

class SiteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SiteServiceContract::class];
    }
}
