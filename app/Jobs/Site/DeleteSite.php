<?php

namespace App\Jobs\Site;

use App\Events\Site\FixSiteServerConfigurations;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Server\Schemas\RemoveServerSchema;
use App\Jobs\Server\SshKeys\RemoveServerSshKey;
use App\Jobs\Server\Workers\RemoveServerWorker;
use App\Jobs\Server\CronJobs\RemoveServerCronJob;
use App\Jobs\Server\Schemas\RemoveServerSchemaUser;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;
use App\Jobs\Server\SslCertificates\RemoveServerSslCertificate;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Jobs\Server\EnvironmentVariables\RemoveServerEnvironmentVariable;

class DeleteSite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ModelCommandTrait;

    private $server;
    private $site;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Site $site
     */
    public function __construct(Server $server, Site $site)
    {
        $this->server = $server;
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function handle(SiteService $siteService, RemoteTaskService $remoteTaskService)
    {
        $serverType = $this->server->type;

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::WORKER_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
        }

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $siteService->deleteSite($this->server, $this->site);
        }

        event(new FixSiteServerConfigurations($this->site));
    }
}
