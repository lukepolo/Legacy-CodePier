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
        if (! empty($sslCertificate->getDirty())) {
            broadcast(new SslCertificateUpdated($sslCertificate))->toOthers();
        }
    }
}
