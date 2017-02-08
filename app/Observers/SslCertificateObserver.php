<?php

namespace App\Observers;

use App\Models\SslCertificate;
use App\Events\SslCertificate\SslCertificateUpdated;

class SslCertificateObserver
{
    /**
     * Listen to the User created event.
     *
     * @param SslCertificate $sslCertificate
     *
     * @return void
     */
    public function updated(SslCertificate $sslCertificate)
    {
        broadcast(new SslCertificateUpdated($sslCertificate))->toOthers();
    }
}
