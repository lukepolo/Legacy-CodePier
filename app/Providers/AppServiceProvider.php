<?php

namespace App\Providers;

use App\Models\Server\ServerCronJob;
use App\Models\Server\ServerFirewallRule;
use App\Models\Server\ServerNetworkRule;
use App\Models\Server\ServerSshKey;
use App\Models\Server\ServerSslCertificate;
use App\Models\Server\ServerWorker;
use App\Models\Site\Site;
use App\Models\Site\SiteCronJob;
use App\Models\Site\SiteFile;
use App\Models\Site\SiteFirewallRule;
use App\Models\Site\SiteSshKey;
use App\Models\Site\SiteSslCertificate;
use App\Models\Site\SiteWorker;
use App\Models\User\User;
use App\Observers\Server\ServerCronJobObserver;
use App\Observers\Server\ServerFirewallRuleObserver;
use App\Observers\Server\ServerNetworkRuleObserver;
use App\Observers\Server\ServerSshKeyObserver;
use App\Observers\Server\ServerSslCertificateObserver;
use App\Observers\Server\ServerWorkerObserver;
use App\Observers\Site\SiteCronJobObserver;
use App\Observers\Site\SiteFileObserver;
use App\Observers\Site\SiteFirewallRuleObserver;
use App\Observers\Site\SiteObserver;
use App\Observers\Site\SiteSshKeyObserver;
use App\Observers\Site\SiteSslCertificateObserver;
use App\Observers\Site\SiteWorkerObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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
        ServerNetworkRule::observe(ServerNetworkRuleObserver::class);
        ServerFirewallRule::observe(ServerFirewallRuleObserver::class);
        ServerSslCertificate::observe(ServerSslCertificateObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
