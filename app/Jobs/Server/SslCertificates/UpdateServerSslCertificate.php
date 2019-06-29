<?php

namespace App\Jobs\Server\SslCertificates;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Site\SiteSslCertificateUpdated;
use App\Contracts\Server\ServerServiceContract as ServerService;

class UpdateServerSslCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sslCertificate;
    private $reloadWebServices;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @param bool $reloadWebServices
     */
    public function __construct(Server $server, SslCertificate $sslCertificate, $reloadWebServices = true)
    {
        $this->server = $server;
        $this->sslCertificate = $sslCertificate;
        $this->reloadWebServices = $reloadWebServices;
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function handle(ServerService $serverService)
    {
        $this->sslCertificate->update([
            'key' => $serverService->getFile($this->server, $this->sslCertificate->key_path),
            'cert' => $serverService->getFile($this->server, $this->sslCertificate->cert_path),
        ]);

        foreach ($this->sslCertificate->sites as $site) {
            broadcast(new SiteSslCertificateUpdated($site, $this->sslCertificate, $this->reloadWebServices));
        }
    }
}
