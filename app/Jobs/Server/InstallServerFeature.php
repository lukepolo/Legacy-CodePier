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

        $this->makeCommand($server, $server, null, 'Installing new server feature '.implode(' ', explode('_', snake_case($feature))).' on server');
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

        $this->runOnServer(function () use ($serverService, $serverFeatures) {
            call_user_func_array([
                    $serverService->getService($this->service, $this->server),
                    'install'.$this->feature,
            ], $this->parameters);
        });

        $serverFeatures[$this->service][$this->feature] = [
            'installing' => false,
        ];

        if($this->wasSuccessful()) {
            $serverFeatures[$this->service][$this->feature] = [
                'enabled' => true,
            ];
        }

        $this->server->update([
            'server_features' => $serverFeatures,
        ]);
    }
}
