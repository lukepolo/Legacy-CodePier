<?php

namespace App\Events\Server;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Models\Server\Server;
use App\Services\Site\SiteService;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Contracts\Site\SiteServiceContract;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;

class UpdateServerFirewallRules
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $this->site->firewallRules->each(function (FirewallRule $firewallRule) {
            $this->installFirewallRule($firewallRule);
        });

        if (
            $this->serverType === SystemService::WEB_SERVER ||
            $this->serverType === SystemService::FULL_STACK_SERVER
        ) {
            $servicesPorts = SystemService::SERVICES_PORTS;

            /** @var SiteService $siteService */
            $siteService = app(SiteServiceContract::class);

            if ($this->site->hasDatabaseServers()) {
                foreach ($site->getDatabases() as $database) {
                    if (isset($servicesPorts[$database])) {
                        foreach ($servicesPorts[$database] as $port) {
                            $siteService->createFirewallRule(
                                $this->site,
                                $port,
                                'both',
                                'Opening port '.$port.' to '.$server->ip.' for '.$database,
                                $this->server->ip
                            );
                        }
                    }
                }
            }

            if ($this->site->hasWorkerServers()) {
                foreach ($site->getWorkers() as $worker) {
                    if (isset($servicesPorts[$worker])) {
                        foreach ($servicesPorts[$worker] as $port) {
                            $siteService->createFirewallRule(
                                $this->site,
                                $port,
                                'both',
                                'Opening port '.$port.' to '.$server->ip.' for '.$worker,
                                $this->server->ip
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @param FirewallRule $firewallRule
     */
    private function installFirewallRule(FirewallRule $firewallRule)
    {
        dispatch(
            (new InstallServerFirewallRule($this->server, $firewallRule, $this->command))->onQueue(config('queue.channels.server_commands'))
        );
    }
}