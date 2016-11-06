<?php

namespace App\Observers\Site;

use App\Models\Server\ServerSslCertificate;
use App\Models\Site\SiteSslCertificate;

class SiteSslCertificateObserver
{
    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function created(SiteSslCertificate $siteSslCertificate)
    {
        foreach ($siteSslCertificate->site->provisionedServers as $server) {
            ServerSslCertificate::create([
                'server_id' => $server->id,
                'type' => $siteSslCertificate->type,
                'active' => true,
                'domains' => $siteSslCertificate->domains,
                'key_path' => $siteSslCertificate->key_path,
                'cert_path' => $siteSslCertificate->cert_path,
                'site_ssl_certificate_id' => $siteSslCertificate->id,
            ]);
        }
    }

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function updating(SiteSslCertificate $siteSslCertificate)
    {
        if ($siteSslCertificate->active) {
            $this->deactivateCurrentSsl($siteSslCertificate->site->activeSsl);
        }
    }

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function updated(SiteSslCertificate $siteSslCertificate)
    {
        $siteSslCertificate->serverSslCertificates->each(function ($serverSslCertificate) use ($siteSslCertificate) {
            $serverSslCertificate->update([
                'active' => $siteSslCertificate->active,
            ]);
        });
    }

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function deleting(SiteSslCertificate $siteSslCertificate)
    {
        $siteSslCertificate->serverSslCertificates->each(function ($serverSslCertificate) {
            $serverSslCertificate->delete();
        });
    }

    private function deactivateCurrentSsl(SiteSslCertificate $activeSsl = null)
    {
        if(!empty($activeSsl)) {
            $activeSsl->update([
                'active' => false,
            ]);
        }
    }
}
