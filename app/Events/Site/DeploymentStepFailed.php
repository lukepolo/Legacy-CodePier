<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\Site\Deployment\DeploymentStep;
use App\Models\Site\Deployment\DeploymentEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class DeploymentStepFailed implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    private $siteId;
    private $siteDeployment;
    private $deploymentEvent;
    private $serverDeployment;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Site\Site $site
     * @param Server $server
     * @param \App\Models\Site\Deployment\DeploymentEvent $deploymentEvent
     * @param DeploymentStep $deploymentStep
     * @param $log
     */
    public function __construct(Site $site, Server $server, DeploymentEvent $deploymentEvent, DeploymentStep $deploymentStep, $log)
    {
        $this->siteId = $site->id;

        $deploymentEvent->update([
            'log' => $log,
            'failed' => true,
            'completed' => true,
        ]);

        $deploymentEvent->serverDeployment->update([
            'status' => 'Deployment Failed',
            'failed' => true,
        ]);

        $this->deploymentEvent = $deploymentEvent;
        $this->serverDeployment = $deploymentEvent->serverDeployment;
        $this->serverDeployment->load('siteDeployment');
        $this->siteDeployment = $this->serverDeployment->siteDeployment;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.Site.Site.'.$this->siteId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        strip_relations($this->deploymentEvent)->load('step');

        if (strlen($this->deploymentEvent->log) >= 7000) {
            $this->deploymentEvent->log = substr($this->deploymentEvent->log, 0, 7000)."\n , please reload to see the rest of the log";
        }

        return [
            'site_deployment' => strip_relations($this->siteDeployment),
            'server_deployment' => strip_relations($this->serverDeployment),
            'deployment_event' => $this->deploymentEvent,
        ];
    }
}
