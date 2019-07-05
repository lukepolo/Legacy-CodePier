<?php

namespace App\Events\Site;

use App\Jobs\NullJob;
use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\SslCertificates\ActivateServerSslCertificate;
use App\Jobs\Server\SslCertificates\DeactivateServerSslCertificate;

class SiteSslCertificateUpdated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param SslCertificate $sslCertificate
     */
    public function __construct(Site $site, SslCertificate $sslCertificate)
    {
        if ($site->isLoadBalanced()) {
            $availableServers = $site->filterServersByType([
                SystemService::LOAD_BALANCER,
            ]);
        } else {
            $availableServers = $site->filterServersByType([
                SystemService::WEB_SERVER,
                SystemService::FULL_STACK_SERVER,
            ]);
        }

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $sslCertificate, $sslCertificate->active ? 'Activating' : 'Deactivating');

            $activeSsl = $site->activeSsl();

            $chainJobs = [];

            foreach ($availableServers as $server) {
                if ($sslCertificate->active) {
                    if (!empty($activeSsl) && $activeSsl->id != $sslCertificate->id) {
                        $activeSsl->update([
                            'active' => false,
                        ]);
                        $chainJobs[] = new DeactivateServerSslCertificate($server, $site, $activeSsl, $siteCommand, false);
                    }
                    $chainJobs[] = new ActivateServerSslCertificate($server, $site, $sslCertificate, $siteCommand, false);
                }
                else {
                    $chainJobs[]  = new DeactivateServerSslCertificate($server, $site, $sslCertificate, $siteCommand, false);
                }
            }

            NullJob::withChain($chainJobs)->dispatch()->allOnConnection('sync');
        }
    }
}
