<?php

namespace App\Providers;

use App\Contracts\RemoteTaskServiceContract;
use App\Services\RemoteTaskService;
use Illuminate\Support\ServiceProvider;

class RemoteTaskServiceProvider extends ServiceProvider
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
            RemoteTaskServiceContract::class,
            RemoteTaskService::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [RemoteTaskServiceContract::class];
    }

}
