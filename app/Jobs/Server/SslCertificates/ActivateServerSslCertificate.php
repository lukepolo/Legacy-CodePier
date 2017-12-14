<?php

namespace App\Jobs\Server\SslCertificates;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Site\SiteUpdatedWebConfig;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class ActivateServerSslCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;
    private $sslCertificate;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param Site $site
     * @param SslCertificate $sslCertificate
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, SslCertificate $sslCertificate, Command $siteCommand = null)
    {
        $this->site = $site;
        $this->server = $server;
        $this->sslCertificate = $sslCertificate;
        $this->makeCommand($server, $sslCertificate, $siteCommand, 'Activating');
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        $this->runOnServer(function () use ($serverService, $siteService) {
            $serverService->activateSslCertificate($this->server, $this->sslCertificate);
        });

        event(new SiteUpdatedWebConfig($this->site));
    }
}
