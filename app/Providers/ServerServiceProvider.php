<?php

namespace App\Providers;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Systems\SystemServiceContract;
use App\Services\Server\ServerService;
use App\Services\Systems\SystemService;
use Illuminate\Support\ServiceProvider;

class ServerServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ServerServiceContract::class,
            ServerService::class
        );

        $this->app->bind(
            SystemServiceContract::class,
            SystemService::class
        );
    }
}
