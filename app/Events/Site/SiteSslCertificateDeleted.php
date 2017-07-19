<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\SslCertificates\RemoveServerSslCertificate;

class SiteSslCertificateDeleted
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
        $site->sslCertificates()->detach($sslCertificate);

        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $sslCertificate, 'Removing');

            $loadBalancerExists = $site->provisionedServers->first(function ($server) {
                return $server->type === SystemService::LOAD_BALANCER;
            });

            foreach ($site->provisionedServers as $server) {
                $serverType = $server->type;

                if (
                    (
                        empty($loadBalancerExists) &&
                        $serverType === SystemService::WEB_SERVER ||
                        $serverType === SystemService::LOAD_BALANCER
                    ) ||
                    $serverType === SystemService::FULL_STACK_SERVER
                ) {
                    dispatch(
                        (new RemoveServerSslCertificate(
                            $server,
                            $sslCertificate,
                            $siteCommand
                        ))->onQueue(config('queue.channels.server_commands'))
                    );
                }
            }
        }
    }
}
