<?php

namespace App\Providers;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract;
use App\Services\Server\Site\Repository\RepositoryService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
            RepositoryServiceContract::class,
            RepositoryService::class
        );
    }
}
