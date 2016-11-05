<?php

namespace App\Jobs\Server\SslCertificates;

use App\Contracts\Server\ServerServiceContract as ServerService;
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
        $this->serverSslCertificate = $serverSslCertificate;
    }

    /**
     * @param ServerService $serverService
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {

        });

        return $this->remoteResponse();
    }
}
