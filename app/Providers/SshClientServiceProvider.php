<?php

namespace App\Providers;

use App\Contracts\SshContract;
use App\Services\RemoteTaskService;
use Illuminate\Support\ServiceProvider;

class SshClientServiceProvider extends ServiceProvider
{
    protected $defer = true;

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
            SshContract::class,
            RemoteTaskService::class
        );
    }
}
