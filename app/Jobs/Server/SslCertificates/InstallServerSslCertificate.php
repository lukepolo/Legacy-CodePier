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
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerSslCertificate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;
    private $siteCommand;
    private $sslCertificate;

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
        $this->siteCommand = $siteCommand;
        $this->sslCertificate = $sslCertificate;
        $this->makeCommand($this->server, $this->sslCertificate, $this->siteCommand);
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        if(
            $this->server->sslCertificates
                ->where('type', $this->sslCertificate->type)
                ->where('domains', $this->sslCertificate->domains)
                ->count()
            ||
            $this->server->sslCertificates->keyBy('id')->get($this->sslCertificate->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has ssl certificate installed for '.$this->sslCertificate->domains);
        } else {

            $this->runOnServer(function () use ($serverService, $siteService) {
                $serverService->installSslCertificate($this->server, $this->sslCertificate);
                $siteService->updateWebServerConfig($this->server, $this->site);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            } else {
                $this->server->sslCertificates()->save($this->sslCertificate);
            }

        }
    }
}
