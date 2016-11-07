<?php

namespace App\Jobs\Server\SslCertificates;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Models\Server\ServerSslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActivateServerSslCertificate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $serverSslCertificate;

    /**
     * InstallServerWorker constructor.
     * @param ServerSslCertificate $serverSslCertificate
     */
    public function __construct(ServerSslCertificate $serverSslCertificate)
    {
        $this->makeCommand($serverSslCertificate);
        $this->serverSslCertificate = $serverSslCertificate;
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        $this->runOnServer(function () use ($serverService, $siteService) {
            $serverService->activateSslCertificate($this->serverSslCertificate);
            $siteService->updateWebServerConfig($this->serverSslCertificate->server, $this->serverSslCertificate->siteSslCertificate->site);
        });

        return $this->remoteResponse();
    }
}
