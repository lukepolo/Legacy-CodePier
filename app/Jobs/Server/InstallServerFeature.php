<?php

namespace App\Jobs\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Server\ServerFeatureInstalled;
use App\Contracts\Server\ServerFeatureServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerFeature implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

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

        $serverFeatureService = app(ServerFeatureServiceContract::class);

        $this->makeCommand($server, $server, null, 'Installing '.$serverFeatureService->getBaseFeatures()->get($service)->get($feature)->get('name').' on server');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
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

        if ($this->wasSuccessful()) {
            $serverFeatures[$this->service][$this->feature] = [
                'enabled' => true,
            ];
        }

        $this->server->update([
            'server_features' => $serverFeatures,
        ]);

        broadcast(new ServerFeatureInstalled($this->server));
    }
}
