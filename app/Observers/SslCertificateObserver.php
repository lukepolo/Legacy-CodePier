<?php

namespace App\Observers;

use App\Events\SslCertificate\SslCertificateUpdated;
use App\Models\SslCertificate;

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
        broadcast(new SslCertificateUpdated($sslCertificate));
    }
}
