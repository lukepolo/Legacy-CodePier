<?php

namespace App\Observers\Site;

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

    }

    /**
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function deleting(SiteSslCertificate $siteSslCertificate)
    {

    }
}
