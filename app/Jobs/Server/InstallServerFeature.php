<?php

namespace App\Jobs\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerFeature implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $feature;
    private $service;
    private $parameters;

    public $tries = 1;
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param $feature
     * @param $service
     * @param $parameters
     */
    public function __construct(Server $server, $feature, $service, $parameters)
    {
        $this->server = $server;
        $this->feature = $feature;
        $this->service = $service;
        $this->parameters = $parameters;

        // TODO - add server command // this may have to be its own model? not sure
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        $serverFeatures = $this->server->server_features;

        if (! isset($serverFeatures[$this->service][$this->feature]) || ! $serverFeatures[$this->service][$this->feature]['enabled']) {
            $this->runOnServer(function () use ($serverService, $serverFeatures) {
                $serverFeatures[$this->service][$this->feature] = [
                    'enabled' => true,
                ];

                call_user_func_array([
                        $serverService->getService($this->service, $this->server),
                        'install'.$this->feature,
                ], $this->parameters);

                $this->server->update([
                    'server_features' => $serverFeatures,
                ]);
            });
        }
    }
}
