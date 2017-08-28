<?php

namespace App\Jobs\Server\SslCertificates;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\SslCertificate;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Site\SiteSslCertificateUpdated;
use App\Contracts\Server\ServerServiceContract as ServerService;

class UpdateServerSslCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sslCertificate;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param SslCertificate $sslCertificate
     */
    public function __construct(Server $server, SslCertificate $sslCertificate)
    {
        $this->server = $server;
        $this->sslCertificate = $sslCertificate;
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
            event(new SiteSslCertificateUpdated($site, $this->sslCertificate));
        }
    }
}
