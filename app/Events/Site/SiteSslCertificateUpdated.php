<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
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
        $activeSsl = $site->activeSsl();
        $siteCommand = $this->makeCommand($site, $sslCertificate);

        foreach ($site->provisionedServers as $server) {
            if ($sslCertificate->active) {
                if ($activeSsl->id != $sslCertificate->id) {
                    dispatch(
                        (new DeactivateServerSslCertificate($server, $site, $activeSsl, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
                    );
                }

                dispatch(
                    (new ActivateServerSslCertificate($server, $site, $sslCertificate, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            } else {
                dispatch(
                    (new DeactivateServerSslCertificate($server, $site, $sslCertificate, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            }
        }
    }
}
