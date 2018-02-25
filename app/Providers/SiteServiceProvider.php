<?php

namespace App\Providers;

use App\Services\Site\SiteService;
use Illuminate\Support\ServiceProvider;
use App\Services\Site\SiteFeatureService;
use App\Contracts\Site\SiteServiceContract;
use App\Services\Site\SiteDeploymentStepsService;
use App\Contracts\Site\SiteFeatureServiceContract;
use App\Services\Site\SiteLanguageSettingsService;
use App\Contracts\Site\SiteDeploymentStepsServiceContract;
use App\Contracts\Site\SiteLanguageSettingsServiceContract;

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

        $this->app->bind(
            SiteFeatureServiceContract::class,
            SiteFeatureService::class
        );

        $this->app->bind(
            SiteDeploymentStepsServiceContract::class,
            SiteDeploymentStepsService::class
        );

        $this->app->bind(
            SiteLanguageSettingsServiceContract::class,
            SiteLanguageSettingsService::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            SiteServiceContract::class,
            SiteFeatureServiceContract::class,
            SiteDeploymentStepsServiceContract::class,
            SiteLanguageSettingsServiceContract::class,
        ];
    }
}
