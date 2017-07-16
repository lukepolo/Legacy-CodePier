<?php

namespace App\Jobs\Server\SslCertificates;

use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Site\SiteUpdatedWebConfig;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerSslCertificate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sslCertificate;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @param Command $siteCommand
     */
    public function __construct(Server $server, SslCertificate $sslCertificate, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->sslCertificate = $sslCertificate;
        $this->makeCommand($server, $sslCertificate, $siteCommand, 'Removing');
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

                foreach ($this->sslCertificate->sites as $site) {
                    event(new SiteUpdatedWebConfig($site));
                }
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }

            $this->server->sslCertificates()->detach($this->sslCertificate->id);

            $this->sslCertificate->load('servers');

            if ($this->sslCertificate->servers->count() == 0) {
                $this->sslCertificate->delete();
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this ssl certificate', false);
        }
    }
}
