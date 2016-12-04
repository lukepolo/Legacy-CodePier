<?php

namespace App\Observers\Site;

use App\Models\Site\SiteSshKey;
use App\Traits\ModelCommandTrait;
use App\Models\Server\ServerSshKey;

class SiteSshKeyObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteSshKey $siteSshKey
     */
    public function created(SiteSshKey $siteSshKey)
    {
        foreach ($siteSshKey->site->provisionedServers as $server) {
            if (! ServerSshKey::where('name', $siteSshKey->name)
                ->where('ssh_key', $siteSshKey->ssh_key)
                ->count()
            ) {
                $serverSshKey = new ServerSshKey([
                    'server_id' => $server->id,
                    'name' => $siteSshKey->name,
                    'ssh_key' => $siteSshKey->ssh_key,
                    'site_ssh_key_id' => $siteSshKey->id,
                ]);

                $serverSshKey->addHidden([
                    'command' => $this->makeCommand($siteSshKey),
                ]);

                $serverSshKey->save();
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
