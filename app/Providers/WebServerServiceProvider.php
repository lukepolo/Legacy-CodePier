<?php

namespace App\Providers;

use App\Contracts\WebServers\NginxWebServerServiceContract;
use App\Services\Systems\WebServers\NginxWebServerService;
use Illuminate\Support\ServiceProvider;

class WebServerServiceProvider extends ServiceProvider
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
            NginxWebServerServiceContract::class,
            NginxWebServerService::class
        );
    }
}
