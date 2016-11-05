<?php

namespace App\Observers\Server;

use App\Jobs\Server\SslCertificates\ActivateServerSslCertificate;
use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;
use App\Jobs\Server\SslCertificates\RemoveServerSslCertificate;
use App\Models\Server\ServerSslCertificate;

class ServerSslCertificateObserver
{
    /**
     * @param ServerSslCertificate $serverSslCertificate
     */
    public function created(ServerSslCertificate $serverSslCertificate)
    {
        dispatch(new InstallServerSslCertificate($serverSslCertificate));
    }

    public function updated(ServerSslCertificate $serverSslCertificate)
    {
        if($serverSslCertificate->active) {
            dispatch(new ActivateServerSslCertificate($serverSslCertificate));
        }
    }

    /**
     * @param ServerSslCertificate $serverSslCertificate
     */
    public function deleting(ServerSslCertificate $serverSslCertificate)
    {
        dispatch(new RemoveServerSslCertificate($serverSslCertificate));
    }
}
