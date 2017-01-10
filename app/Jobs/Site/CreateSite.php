<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\UpdateServerFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Jobs\Server\Workers\InstallServerWorker;
use App\Jobs\Server\CronJobs\InstallServerCronJob;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;
use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class CreateSite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ModelCommandTrait;

    private $server;
    private $site;

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
        $siteService->create($this->server, $this->site);

        $this->site->cronJobs->each(function ($cronJob) {
            dispatch(
                (new InstallServerCronJob($this->server, $cronJob, $this->makeCommand($this->site, $cronJob)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        });

        $this->site->files->each(function ($file) {
            if (! empty($file->content)) {
                dispatch(
                    (new UpdateServerFile($this->server, $file, $this->makeCommand($this->site, $file)))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            }
        });

        $this->site->firewallRules->each(function ($firewallRule) {
            dispatch(
                (new InstallServerFirewallRule($this->server, $firewallRule, $this->makeCommand($this->site, $firewallRule)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        });

        $this->site->sshKeys->each(function ($sshKey) {
            dispatch(
                (new InstallServerSshKey($this->server, $sshKey, $this->makeCommand($this->site, $sshKey)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        });

        $this->site->sslCertificates->each(function ($sslCertificate) {
            dispatch(
                (new InstallServerSslCertificate($this->server, $this->site, $sslCertificate, $this->makeCommand($this->site, $sslCertificate)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        });

        $this->site->workers->each(function ($worker) {
            dispatch(
                (new InstallServerWorker($this->server, $worker, $this->makeCommand($this->site, $worker)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        });

        $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
    }
}
