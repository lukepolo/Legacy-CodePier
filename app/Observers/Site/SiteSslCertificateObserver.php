<?php

namespace App\Observers\Site;

use App\Traits\ModelCommandTrait;
use App\Models\Site\SiteSslCertificate;
use App\Models\Server\ServerSslCertificate;

class SiteSslCertificateObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function created(SiteSslCertificate $siteSslCertificate)
    {
        foreach ($siteSslCertificate->site->provisionedServers as $server) {
            if (! ServerSslCertificate::where('server_id', $server->id)
                ->where('type', $siteSslCertificate->type)
                ->where('domains', $siteSslCertificate->domains)
                ->count()
            ) {
                $serverSslCertificate = new ServerSslCertificate([
                    'active' => true,
                    'server_id' => $server->id,
                    'type' => $siteSslCertificate->type,
                    'domains' => $siteSslCertificate->domains,
                    'key_path' => $siteSslCertificate->key_path,
                    'cert_path' => $siteSslCertificate->cert_path,
                    'site_ssl_certificate_id' => $siteSslCertificate->id,
                ]);

                $serverSslCertificate->addHidden([
                    'command' => $this->makeCommand($siteSslCertificate),
                ]);

                $serverSslCertificate->save();
            } else {
                $siteSslCertificate->delete();
            }
        }
    }

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function updating(SiteSslCertificate $siteSslCertificate)
    {
        if ($siteSslCertificate->active) {
            $this->deactivateCurrentSsl($siteSslCertificate->site->activeSsl, $siteSslCertificate);
        }
    }

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function updated(SiteSslCertificate $siteSslCertificate)
    {
        $siteSslCertificate->serverSslCertificates->each(function ($serverSslCertificate) use ($siteSslCertificate) {

            $serverSslCertificate->addHidden([
                'command' => $this->makeCommand($siteSslCertificate),
            ]);

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
        $siteSslCertificate->serverSslCertificates->each(function ($serverSslCertificate) use($siteSslCertificate) {

            $serverSslCertificate->addHidden([
                'command' => $this->makeCommand($siteSslCertificate),
            ]);

            $serverSslCertificate->delete();
        });
    }

    private function deactivateCurrentSsl(SiteSslCertificate $activeSsl = null, $siteSslCertificate)
    {
        if (! empty($activeSsl)) {

            $activeSsl->addHidden([
                'command' => $this->makeCommand($siteSslCertificate),
            ]);

            $activeSsl->update([
                'active' => false,
            ]);
        }
    }
}
