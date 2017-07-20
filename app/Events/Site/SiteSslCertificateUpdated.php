<?php

namespace App\Events\Site;

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
        if ($site->provisionedServers->count()) {
            $activeSsl = $site->activeSsl();
            $siteCommand = $this->makeCommand($site, $sslCertificate, $sslCertificate->active ? 'Activating' : 'Deactivating');

            $loadBalancerExists = $site->servers->first(function ($server) {
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
                    if ($sslCertificate->active) {
                        if ($activeSsl->id != $sslCertificate->id) {
                            $activeSsl->update([
                                'active' => false,
                            ]);

                            dispatch(
                                (new DeactivateServerSslCertificate(
                                    $server,
                                    $site,
                                    $activeSsl,
                                    $siteCommand
                                ))->onQueue(config('queue.channels.server_commands'))
                            );
                        }

                        dispatch(
                            (new ActivateServerSslCertificate(
                                $server,
                                $site,
                                $sslCertificate,
                                $siteCommand
                            ))->onQueue(config('queue.channels.server_commands'))
                        );
                    } else {
                        dispatch(
                            (new DeactivateServerSslCertificate(
                                $server,
                                $site,
                                $sslCertificate,
                                $siteCommand
                            ))->onQueue(config('queue.channels.server_commands'))
                        );
                    }
                }
            }
        }
    }
}
