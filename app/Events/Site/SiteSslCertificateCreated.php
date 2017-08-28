<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;

class SiteSslCertificateCreated
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
            $siteCommand = $this->makeCommand($site, $sslCertificate, 'Setting Up');

            foreach ($availableServers as $server) {
                dispatch(
                    (new InstallServerSslCertificate($server, $sslCertificate, $siteCommand)
                    )->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
