<?php

namespace App\Jobs\Site;

use App\Jobs\Server\CronJobs\RemoveServerCronJob;
use App\Jobs\Server\EnvironmentVariables\RemoveServerEnvironmentVariable;
use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;
use App\Jobs\Server\Schemas\RemoveServerSchema;
use App\Jobs\Server\SshKeys\RemoveServerSshKey;
use App\Jobs\Server\SslCertificates\RemoveServerSslCertificate;
use App\Jobs\Server\Workers\RemoveServerWorker;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class CreateSite implements ShouldQueue
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

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->cronJobs->each(function ($cronJob) {
                dispatch(
                    (new RemoveServerCronJob($this->server, $cronJob,
                        $this->makeCommand($this->site, $cronJob)))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        $this->site->firewallRules->each(function ($firewallRule) {
            dispatch(
                (new RemoveServerFirewallRule($this->server, $firewallRule, $this->makeCommand($this->site,
                    $firewallRule)))->onQueue(config('queue.channels.server_commands'))
            );
        });

        $this->site->sshKeys->each(function ($sshKey) {
            dispatch(
                (new RemoveServerSshKey($this->server, $sshKey, $this->makeCommand($this->site, $sshKey)))->onQueue(config('queue.channels.server_commands'))
            );
        });

        if (
            (
                ! $this->site->isLoadBalanced() &&
                (
                    $serverType === SystemService::WEB_SERVER ||
                    $serverType === SystemService::FULL_STACK_SERVER
                )
            ) ||
            $serverType === SystemService::LOAD_BALANCER
        ) {
            $this->site->sslCertificates->each(function ($sslCertificate) {
                dispatch(
                    (new RemoveServerSslCertificate($this->server, $sslCertificate, $this->makeCommand($this->site,
                        $sslCertificate)))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        if (
            $serverType === SystemService::WORKER_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->workers->each(function ($worker) {
                dispatch(
                    (new RemoveServerWorker($this->server, $worker,
                        $this->makeCommand($this->site, $worker)))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        if (
            $serverType === SystemService::DATABASE_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->schemas->each(function ($schema) {
                dispatch(
                    (new RemoveServerSchema($this->server, $schema,
                        $this->makeCommand($this->site, $schema)))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        $this->site->environmentVariables->each(function ($environmentVariable) {
            dispatch(
                (new RemoveServerEnvironmentVariable($this->server, $environmentVariable, $this->makeCommand($this->site, $environmentVariable)))->onQueue(config('queue.channels.server_commands'))
            );
        });

    }
}
