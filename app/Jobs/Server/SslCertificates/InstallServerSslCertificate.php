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
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerSslCertificate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sslCertificate;

    public $tries = 1;
    public $timeout = 120;

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
        $this->makeCommand($server, $sslCertificate, $siteCommand);
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        if ($this->server->sslCertificates->keyBy('id')->get($this->sslCertificate->id)) {

            $this->updateServerCommand(0, 'Sever already has ssl certificate installed for '.$this->sslCertificate->domains);

        } else {

            $this->runOnServer(function () use ($serverService, $siteService) {
                $serverService->installSslCertificate($this->server, $this->sslCertificate);

                foreach ($this->sslCertificate->sites as $site) {
                    $siteService->updateWebServerConfig($this->server, $site);
                }
            });

            if (! $this->wasSuccessful()) {

                $this->sslCertificate->update([
                    'failed' => true,
                ]);

                throw new ServerCommandFailed($this->getCommandErrors());
            }

            $this->sslCertificate->update([
                'failed' => false,
            ]);

            $this->server->sslCertificates()->save($this->sslCertificate);
        }
    }
}
