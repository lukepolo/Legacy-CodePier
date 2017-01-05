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

class RemoveServerSslCertificate implements ShouldQueue
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
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        $sitesCount = $this->sslCertificate->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService, $siteService) {
                $serverService->removeSslCertificate($this->server, $this->sslCertificate);
                $siteService->updateWebServerConfig($this->server, $this->site);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        }

        $this->server->sslCertificates()->detach($this->sslCertificate->id);

        if (! $sitesCount) {
            $this->sslCertificate->load('servers');
            if ($this->sslCertificate->servers->count() == 0) {
                $this->sslCertificate->delete();
            }
        }
    }
}
