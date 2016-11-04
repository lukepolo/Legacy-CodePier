<?php

namespace App\Observers\Server;

use App\Models\Server\ServerSslCertificate;

/**
 * Class ServerSslCertificateObserver.
 */
class ServerSslCertificateObserver
{
    /**
     * @param ServerSslCertificate $serverSslCertificate
     */
    public function created(ServerSslCertificate $serverSslCertificate)
    {
    }

    /**
     * @param ServerSslCertificate $serverSslCertificate
     */
    public function deleting(ServerSslCertificate $serverSslCertificate)
    {
    }
}
