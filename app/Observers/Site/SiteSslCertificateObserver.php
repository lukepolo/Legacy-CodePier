<?php

namespace App\Observers\Site;

use App\Models\Server\ServerSslCertificate;
use App\Models\Site\SiteSslCertificate;

/**
 * Class SiteSslCertificateObserver.
 */
class SiteSslCertificateObserver
{
    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function created(SiteSslCertificate $siteSslCertificate)
    {
        foreach($siteSslCertificate->site->servers as $server) {
            ServerSslCertificate::create([
                'server_id' => $server->id,
                'type' => $siteSslCertificate->type,
                'active' => $siteSslCertificate->active,
                'domains' => $siteSslCertificate->domains,
                'key_path' => $siteSslCertificate->key_path,
                'cert_path' => $siteSslCertificate->cert_path,
                'site_ssl_certificate_id' => $siteSslCertificate->id
            ]);
        }
    }

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function deleting(SiteSslCertificate $siteSslCertificate)
    {
        $siteSslCertificate->serverSslCertificates->each(function($serverSslCertificate) {
            $serverSslCertificate->delete();
        });
    }
}
