<?php

namespace App\Jobs\Server\SslCertificates;

use App\Exceptions\ServerCommandFailed;
use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class ActivateServerSslCertificate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;
    private $sslCertificate;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param Site $site
     * @param SslCertificate $sslCertificate
     * @param Command $siteCommand
     * @internal param ServerSslCertificate $serverSslCertificate
     */
    public function __construct(Server $server, Site $site, SslCertificate $sslCertificate, Command $siteCommand = null)
    {
        $this->site = $site;
        $this->server = $server;
        $this->sslCertificate = $sslCertificate;
        $this->makeCommand($server, $sslCertificate, $siteCommand);
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        $this->runOnServer(function () use ($serverService, $siteService) {
            $serverService->activateSslCertificate($this->server, $this->sslCertificate);
            $siteService->updateWebServerConfig($this->server, $this->site);
        });

        if (! $this->wasSuccessful()) {
            throw new ServerCommandFailed($this->getCommandErrors());
        }
    }
}
