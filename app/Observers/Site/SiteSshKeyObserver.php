<?php

namespace App\Observers\Site;

use App\Models\Server\ServerSshKey;
use App\Models\Site\SiteSshKey;

/**
 * Class SiteSshKeyObserver.
 */
class SiteSshKeyObserver
{
    /**
     * @param SiteSshKey $siteSshKey
     */
    public function created(SiteSshKey $siteSshKey)
    {
        foreach ($siteSshKey->site->servers as $server) {
            ServerSshKey::create([
                'key' => $siteSshKey->key,
                'server_id' => $server->id,
                'name' => $siteSshKey->name,
                'site_ssh_key_id' => $siteSshKey->id,
            ]);
        }
    }

    /**
     * @param SiteSshKey $siteSshKey
     */
    public function deleting(SiteSshKey $siteSshKey)
    {
        $siteSshKey->serverSshKeys->each(function ($serverSshKey) {
            $serverSshKey->delete();
        });
    }
}
