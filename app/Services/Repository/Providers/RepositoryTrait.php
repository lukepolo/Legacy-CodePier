<?php

namespace App\Services\Repository\Providers;

use App\Models\Site\Site;

trait RepositoryTrait
{
    /**
     * Marks a repository private.
     *
     * @param Site $site
     * @param $isPrivate
     */
    private function isPrivate(Site $site, $isPrivate)
    {
        $site->update([
            'private' =>  $isPrivate,
        ]);
    }
}
