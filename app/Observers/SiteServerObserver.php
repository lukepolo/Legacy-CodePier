<?php

namespace App\Observers;

use App\Models\Site;

/**
 * Class SiteObserver
 * @package App\Observers
 */
class SiteServerObserver
{
    public function saved(Site $site)
    {
        dump('here');
        dump($site->getOriginal());
        dd($site->getDirty());
    }

}