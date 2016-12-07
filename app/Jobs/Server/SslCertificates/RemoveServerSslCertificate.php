<?php

namespace App\Jobs\Server\SslCertificates;

use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Server\ServerSslCertificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerSslCertificate implements ShouldQueue
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
            $serverService->removeSslCertificate($this->serverSslCertificate);
            $siteService->updateWebServerConfig($this->serverSslCertificate->server, $this->serverSslCertificate->siteSslCertificate->site);
        });

        return $this->remoteResponse();
    }
}
