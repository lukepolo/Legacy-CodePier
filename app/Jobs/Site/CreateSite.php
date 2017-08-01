<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\UpdateServerFile;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Events\Server\UpdateServerConfigurations;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Jobs\Server\EnvironmentVariables\InstallServerEnvironmentVariable;

class CreateSite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ModelCommandTrait;

    private $site;
    private $server;
    private $command;

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
        $this->site = $site;
        $this->server = $server;
        $this->command = $this->makeCommand($this->site, $this->server, 'Setting up Server '.$server->name.' for '.$site->name);
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
            $siteService->create($this->server, $this->site);
        }

        $this->site->files->each(function ($file) {
            if (! empty($file->content)) {
                dispatch(
                    (new UpdateServerFile($this->server, $file, $this->command))->onQueue(config('queue.channels.server_commands'))
                );
            }
        });

        $this->site->sshKeys->each(function ($sshKey) {
            dispatch(
                (new InstallServerSshKey($this->server, $sshKey, $this->command))->onQueue(config('queue.channels.server_commands'))
            );
        });

        $this->site->environmentVariables->each(function ($environmentVariable) {
            dispatch(
                (new InstallServerEnvironmentVariable($this->server, $environmentVariable, $this->command))->onQueue(config('queue.channels.server_commands'))
            );
        });

        event(new UpdateServerConfigurations($this->server, $this->site, $this->command));
    }
}
