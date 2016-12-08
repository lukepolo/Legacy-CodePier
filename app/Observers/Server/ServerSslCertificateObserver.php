<?php

namespace App\Observers\Server;

use App\Models\Server\ServerSslCertificate;
use App\Jobs\Server\SslCertificates\RemoveServerSslCertificate;
use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;
use App\Jobs\Server\SslCertificates\ActivateServerSslCertificate;
use App\Jobs\Server\SslCertificates\DeactivateServerSslCertificate;

class ServerSslCertificateObserver
{
    /**
     * @param ServerSslCertificate $serverSslCertificate
     */
    public function created(ServerSslCertificate $serverSslCertificate)
    {
        dispatch(
            (new InstallServerSslCertificate($serverSslCertificate))->onQueue('SERVER_COMMAND_QUEUE')
        );
    }

    public function updated(ServerSslCertificate $serverSslCertificate)
    {
        if ($serverSslCertificate->active) {
            dispatch(
                (new ActivateServerSslCertificate($serverSslCertificate))->onQueue('SERVER_COMMAND_QUEUE')
            );
        } else {
            dispatch(
                (new DeactivateServerSslCertificate($serverSslCertificate))->onQueue('SERVER_COMMAND_QUEUE')
            );
        }
    }

    /**
     * @param ServerSslCertificate $serverSslCertificate
     */
    public function deleting(ServerSslCertificate $serverSslCertificate)
    {
        dispatch(
            (new RemoveServerSslCertificate($serverSslCertificate))->onQueue('SERVER_COMMAND_QUEUE')
        );
    }
}
