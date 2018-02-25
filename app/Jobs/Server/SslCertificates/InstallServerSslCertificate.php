<?php

namespace App\Jobs\Server\SslCertificates;

use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Site\SiteUpdatedWebConfig;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Site\SiteSslCertificateUpdated;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerSslCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sslCertificate;

    public $tries = 1;
    public $timeout = 120;

    /**
     * InstallServerWorker constructor.
     *
     * @param Server         $server
     * @param SslCertificate $sslCertificate
     * @param Command        $siteCommand
     */
    public function __construct(Server $server, SslCertificate $sslCertificate, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->sslCertificate = $sslCertificate;
        $this->makeCommand($server, $sslCertificate, $siteCommand, 'Installing');
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function handle(ServerService $serverService)
    {
        if ($this->server->sslCertificates->keyBy('id')->get($this->sslCertificate->id)) {
            $this->updateServerCommand(0, 'Sever already has ssl certificate installed for '.$this->sslCertificate->domains);
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->installSslCertificate($this->server, $this->sslCertificate);
            });

            if (! $this->wasSuccessful()) {
                $this->sslCertificate->update([
                    'failed' => true,
                ]);

                if ($this->sslCertificate->active) {
                    $this->sslCertificate->update([
                        'active' => false,
                    ]);
                    $this->updateWebConfigs();
                }

                return false;
            }

            $this->sslCertificate->update([
                'failed' => false,
            ]);

            $this->server->sslCertificates()->save($this->sslCertificate);

            $this->updateWebConfigs();
        }
    }

    private function updateWebConfigs()
    {
        foreach ($this->sslCertificate->sites as $site) {
            if (true === $this->sslCertificate->active) {
                broadcast(new SiteSslCertificateUpdated($site, $this->sslCertificate));
            } else {
                event(new SiteUpdatedWebConfig($site));
            }
        }
    }
}
