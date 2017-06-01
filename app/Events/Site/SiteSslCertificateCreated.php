<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Services\Systems\SystemService;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
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
        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $sslCertificate);

            foreach ($site->provisionedServers as $server) {

                $serverType = $server->type;

                if(
                    $serverType === SystemService::WEB_SERVER ||
                    $serverType === SystemService::LOAD_BALANCER ||
                    $serverType === SystemService::FULL_STACK_SERVER
                ) {
                    dispatch(
                        (
                        new InstallServerSslCertificate(
                            $server,
                            $sslCertificate,
                            $siteCommand
                        )
                        )->onQueue(config('queue.channels.server_commands'))
                    );
                }

            }
        }
    }
}
