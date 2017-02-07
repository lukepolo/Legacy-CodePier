<?php

namespace App\Jobs\Server\SslCertificates;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Exceptions\FailedCommand;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerSslCertificate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;
    private $sslCertificate;

    public $tries = 1;
    public $timeout = 120;

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
     * @throws FailedCommand
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        if (
            $this->server->sslCertificates
                ->where('type', $this->sslCertificate->type)
                ->where('domains', $this->sslCertificate->domains)
                ->count()
            ||
            $this->server->sslCertificates->keyBy('id')->get($this->sslCertificate->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has ssl certificate installed for '.$this->sslCertificate->domains);
        } else {

            try {
                $this->runOnServer(function () use ($serverService, $siteService) {
                    $serverService->installSslCertificate($this->server, $this->sslCertificate);
                });
            } catch (FailedCommand $e) {
                $this->sslCertificate->update([
                    'status' => 'Please look at the events bar',
                ]);

                throw $e;
            }

            $this->server->sslCertificates()->save($this->sslCertificate);
        }
    }
}
