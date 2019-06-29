<?php

namespace App\Jobs\Server\SslCertificates;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class ActivateServerSslCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;
    private $siteCommand;
    private $sslCertificate;
    private $reloadWebServices;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param Site $site
     * @param SslCertificate $sslCertificate
     * @param Command $siteCommand
     * @param bool $reloadWebServices
     */
    public function __construct(Server $server, Site $site, SslCertificate $sslCertificate, Command $siteCommand = null, $reloadWebServices = true)
    {
        $this->site = $site;
        $this->server = $server;
        $this->sslCertificate = $sslCertificate;
        $this->reloadWebServices = $reloadWebServices;
        $this->makeCommand($server, $sslCertificate, $siteCommand, 'Activating');
        $this->siteCommand = $siteCommand;
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Site\SiteService $siteService | SiteService $siteService
     * @throws \Exception
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        $this->runOnServer(function () use ($serverService, $siteService) {
            $serverService->activateSslCertificate($this->server, $this->sslCertificate);
        });

        $siteService->updateWebServerConfig($this->server, $this->site, $this->reloadWebServices);
    }
}
