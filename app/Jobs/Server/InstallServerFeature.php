<?php

namespace App\Jobs;

use App\Models\Server\Server;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstallServerFeature implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $feature;
    private $service;
    private $parameters;

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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->runOnServer(function() {

            call_user_func_array(
                [
                    $this->serverService->getService($this->service, $this->server),
                    'install'.$this->feature
                ],
                $this->parameters
            );

            $serverFeatures = $this->server->server_features;

            $serverFeatures[$this->service][$this->feature]['enabled'] = true;

            $this->server->update([
                'server_features' => $serverFeatures
            ]);
        });
    }
}
