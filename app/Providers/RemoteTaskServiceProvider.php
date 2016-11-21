<?php

namespace App\Providers;

use App\Contracts\RemoteTaskServiceContract;
use App\Services\RemoteTaskService;
use Illuminate\Support\ServiceProvider;

class RemoteTaskServiceProvider extends ServiceProvider
{
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
}
