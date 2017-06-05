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
use App\Jobs\Server\Schemas\AddServerSchema;
use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Jobs\Server\UpdateServerLanguageSetting;
use App\Jobs\Server\Workers\InstallServerWorker;
use App\Jobs\Server\CronJobs\InstallServerCronJob;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;
use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Jobs\Server\EnvironmentVariables\InstallServerEnvironmentVariable;

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
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
            $siteService->create($this->server, $this->site);
        }

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->cronJobs->each(function ($cronJob) {
                dispatch(
                    (new InstallServerCronJob($this->server, $cronJob,
                        $this->makeCommand($this->site, $cronJob)))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        $this->site->files->each(function ($file) {
            if (! empty($file->content)) {
                dispatch(
                    (new UpdateServerFile($this->server, $file, $this->makeCommand($this->site, $file)))->onQueue(config('queue.channels.server_commands'))
                );
            }
        });

        $seconds = 0;

        foreach($this->site->firewallRules as $firewallRule) {
            dispatch(
                (new InstallServerFirewallRule($this->server, $firewallRule, $this->makeCommand($this->site,
                    $firewallRule)))->onQueue(config('queue.channels.server_commands'))->delay($seconds)
            );
            $seconds += 10;
        }

        $this->site->sshKeys->each(function ($sshKey) {
            dispatch(
                (new InstallServerSshKey($this->server, $sshKey, $this->makeCommand($this->site, $sshKey)))->onQueue(config('queue.channels.server_commands'))
            );
        });

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER

        ) {
            $this->site->sslCertificates->each(function ($sslCertificate) {
                dispatch(
                    (new InstallServerSslCertificate($this->server, $sslCertificate, $this->makeCommand($this->site,
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
                    (new InstallServerWorker($this->server, $worker,
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
                    (new AddServerSchema($this->server, $schema,
                        $this->makeCommand($this->site, $schema)))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        $this->site->environmentVariables->each(function ($environmentVariable) {
            dispatch(
                (new InstallServerEnvironmentVariable($this->server, $environmentVariable, $this->makeCommand($this->site, $environmentVariable)))->onQueue(config('queue.channels.server_commands'))
            );
        });

        if (
            $serverType === SystemService::WORKER_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->languageSettings->each(function ($languageSetting) {
                dispatch(
                    (new UpdateServerLanguageSetting($this->server, $languageSetting, $this->makeCommand($this->site,
                        $languageSetting)))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }
    }
}
