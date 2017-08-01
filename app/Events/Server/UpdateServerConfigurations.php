<?php

namespace App\Events\Server;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\Schemas\AddServerSchema;
use App\Jobs\Server\Schemas\AddServerSchemaUser;
use App\Jobs\Server\UpdateServerLanguageSetting;
use App\Jobs\Server\Workers\InstallServerWorker;
use App\Jobs\Server\CronJobs\InstallServerCronJob;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;
use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;

class UpdateServerConfigurations
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;

        $serverType = $server->type;

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->cronJobs->each(function ($cronJob) {
                dispatch(
                    (new InstallServerCronJob($this->server, $cronJob, $this->command))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        $this->site->firewallRules->each(function ($firewallRule) {
            dispatch(
                (new InstallServerFirewallRule($this->server, $firewallRule, $this->command))->onQueue(config('queue.channels.server_commands'))
            );
        });

        if (
            $serverType === SystemService::WORKER_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->workers->each(function ($worker) {
                dispatch(
                    (new InstallServerWorker($this->server, $worker, $this->command))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        if (
            $serverType === SystemService::DATABASE_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->schemas->each(function ($schema) {
                dispatch(
                    (new AddServerSchema($this->server, $schema, $this->command))->onQueue(config('queue.channels.server_commands'))
                );
            });

            $this->site->schemaUsers->each(function ($schemaUser) {
                dispatch(
                    (new AddServerSchemaUser($this->server, $schemaUser, $this->command))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

        if (
            $serverType === SystemService::WORKER_SERVER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->site->languageSettings->each(function ($languageSetting) {
                dispatch(
                    (new UpdateServerLanguageSetting($this->server, $languageSetting, $this->command))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }

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
                    (new InstallServerSslCertificate($this->server, $sslCertificate, $this->command))->onQueue(config('queue.channels.server_commands'))
                );
            });
        }
    }
}
