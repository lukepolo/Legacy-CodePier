<?php

namespace App\Providers;

use App\Contracts\Repository\RepositoryServiceContract;
use App\Services\Repository\RepositoryService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
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
