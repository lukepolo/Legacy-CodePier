<?php

namespace App\Providers;

use App\Models\Site\Site;
use App\Models\User\User;
use App\Models\ServerCommand;
use App\Models\Site\SiteFile;
use App\Models\Site\SiteSshKey;
use App\Models\Site\SiteWorker;
use App\Observers\UserObserver;
use App\Models\Site\SiteCronJob;
use App\Models\Server\ServerSshKey;
use App\Models\Server\ServerWorker;
use App\Models\Server\ServerCronJob;
use App\Observers\Site\SiteObserver;
use App\Models\Site\SiteFirewallRule;
use App\Models\Site\SiteSslCertificate;
use Illuminate\Support\ServiceProvider;
use App\Models\Server\ServerNetworkRule;
use App\Observers\Site\SiteFileObserver;
use App\Models\Server\ServerFirewallRule;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Support\Facades\Validator;
use App\Observers\Site\SiteSshKeyObserver;
use App\Observers\Site\SiteWorkerObserver;
use App\Models\Server\ServerSslCertificate;
use App\Observers\Site\SiteCronJobObserver;
use App\Observers\Server\ServerSshKeyObserver;
use App\Observers\Server\ServerWorkerObserver;
use App\Observers\Server\ServerCommandObserver;
use App\Observers\Server\ServerCronJobObserver;
use App\Observers\Site\SiteFirewallRuleObserver;
use App\Observers\Server\ServerDeploymentObserver;
use App\Observers\Site\SiteSslCertificateObserver;
use App\Observers\Server\ServerNetworkRuleObserver;
use App\Observers\Server\ServerFirewallRuleObserver;
use App\Observers\Server\ServerSslCertificateObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() != 'production') {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        User::observe(UserObserver::class);

        Site::observe(SiteObserver::class);
//        SiteFeatureObserver::
        SiteFile::observe(SiteFileObserver::class);
        SiteSshKey::observe(SiteSshKeyObserver::class);
        SiteWorker::observe(SiteWorkerObserver::class);
        SiteCronJob::observe(SiteCronJobObserver::class);
        SiteFirewallRule::observe(SiteFirewallRuleObserver::class);
        SiteSslCertificate::observe(SiteSslCertificateObserver::class);

        ServerSshKey::observe(ServerSshKeyObserver::class);
        ServerWorker::observe(ServerWorkerObserver::class);
        ServerCronJob::observe(ServerCronJobObserver::class);
        ServerCommand::observe(ServerCommandObserver::class);
        ServerNetworkRule::observe(ServerNetworkRuleObserver::class);
        ServerFirewallRule::observe(ServerFirewallRuleObserver::class);
        ServerSslCertificate::observe(ServerSslCertificateObserver::class);

        SiteServerDeployment::observe(ServerDeploymentObserver::class);

        Validator::extend('domain', function ($attribute, $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                return false;
            }

            return preg_match('/^[\pL\pM\pN\.]+$/u', $value) > 0;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
