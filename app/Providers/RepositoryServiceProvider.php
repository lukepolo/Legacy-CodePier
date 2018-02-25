<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Repository\RepositoryService;
use App\Contracts\Repository\RepositoryServiceContract;

class RepositoryServiceProvider extends ServiceProvider
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
            RepositoryServiceContract::class,
            RepositoryService::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [RepositoryServiceContract::class];
    }
}
