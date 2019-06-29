<?php

namespace App\Events\Site;

use App\Jobs\NullJob;
use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\RestartWebServices;
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
     * @param bool $reloadWebServices
     */
    public function __construct(Site $site, SslCertificate $sslCertificate, $reloadWebServices = true)
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
                        $chainJobs[] = (new DeactivateServerSslCertificate($server, $site, $activeSsl, $siteCommand, $reloadWebServices))
                            ->onQueue(config('queue.channels.server_commands'));
                    }

                    $chainJobs[] = (new ActivateServerSslCertificate($server, $site, $sslCertificate, $siteCommand, $reloadWebServices))
                        ->onQueue(config('queue.channels.server_commands'));
                }
                else {
                        $chainJobs[]  = (new DeactivateServerSslCertificate($server, $site, $sslCertificate, $siteCommand, $reloadWebServices))
                            ->onQueue(config('queue.channels.server_commands'));
                }
            }

            if($reloadWebServices === false) {
                foreach ($availableServers as $server) {
                    $chainJobs[] = new RestartWebServices($server, $siteCommand);
                }
            }


            NullJob::withChain($chainJobs)->dispatch();
        }
    }
}
