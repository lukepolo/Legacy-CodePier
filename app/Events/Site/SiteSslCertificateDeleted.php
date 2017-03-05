<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
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
            $siteCommand = $this->makeCommand($site, $sslCertificate);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new RemoveServerSslCertificate($server, $site, $sslCertificate,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
