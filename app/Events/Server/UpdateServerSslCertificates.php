<?php

namespace App\Events\Server;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Models\SslCertificate;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;

class UpdateServerSslCertificates
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

        $this->site->sslCertificates->each(function (SslCertificate $sslCertificate) {
            if ($this->site->isLoadBalanced()) {
                if (! $sslCertificate->hasServer($this->server) && $this->serverType == SystemService::LOAD_BALANCER) {
                    $this->installSslCertificate($sslCertificate);
                }
            } elseif (! $sslCertificate->hasServer($this->server) && (
                $this->serverType == SystemService::WEB_SERVER ||
                $this->serverType == SystemService::FULL_STACK_SERVER
            )) {
                $this->installSslCertificate($sslCertificate);
            }
        });
    }

    /**
     * @param SslCertificate $sslCertificate
     */
    public function installSslCertificate(SslCertificate $sslCertificate)
    {
        rollback_dispatch(
            (new InstallServerSslCertificate($this->server, $sslCertificate, $this->command))->onQueue(config('queue.channels.server_commands'))
        );
    }
}
