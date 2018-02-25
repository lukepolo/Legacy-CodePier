<?php

namespace App\Events\Site;

use App\Jobs\Server\UpdateServerFile;
use App\Jobs\Site\DeploySite;
use App\Jobs\Site\RenameSiteDomain;
use App\Models\Site\Site;
use App\Services\Systems\SystemService;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;

class SiteRenamed
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param $newDomain
     * @param $oldDomain
     */
    public function __construct(Site $site, $newDomain, $oldDomain)
    {
        $availableServers = $site->filterServersByType([
            SystemService::WEB_SERVER,
            SystemService::LOAD_BALANCER,
            SystemService::FULL_STACK_SERVER,
        ]);

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $site, 'Renaming site '.$oldDomain.' to '.$newDomain);

            foreach ($availableServers as $server) {
                foreach ($site->files->filter(function ($value) {
                    return $value->framework_file || $value->custom;
                }) as $siteFile) {
                    $chainedCommands[] = (new UpdateServerFile($server, $siteFile, $siteCommand))->onQueue(config('queue.channels.server_commands'));
                }

                $chainedCommands[] = (new DeploySite($site))->onQueue(config('queue.channels.site_deployments'));

                RenameSiteDomain::withChain($chainedCommands)
                    ->dispatch($server, $site, $newDomain, $oldDomain, $siteCommand)
                    ->onQueue(config('queue.channels.server_commands'));
            }
        }
    }
}
