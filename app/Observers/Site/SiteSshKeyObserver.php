<?php

namespace App\Observers\Site;

use App\Models\Server\ServerSshKey;
use App\Models\Site\SiteSshKey;

class SiteSshKeyObserver
{
    /**
     * @param SiteSshKey $siteSshKey
     */
    public function created(SiteSshKey $siteSshKey)
    {
        foreach ($siteSshKey->site->provisionedServers as $server) {

            if(!ServerSshKey::where('name', $siteSshKey->name)
                ->where('ssh_key', $siteSshKey->ssh_key)
                ->count()
            ) {
                ServerSshKey::create([
                    'server_id' => $server->id,
                    'name' => $siteSshKey->name,
                    'ssh_key' => $siteSshKey->ssh_key,
                    'site_ssh_key_id' => $siteSshKey->id,
                ]);
            }
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
