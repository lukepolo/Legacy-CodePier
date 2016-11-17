<?php

namespace App\Events\Site;

use App\Models\Server\Server;
use App\Models\Site\Deployment\DeploymentEvent;
use App\Models\Site\Deployment\DeploymentStep;
use App\Models\Site\Site;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class DeploymentStepFailed implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $step;
    public $siteId;
    public $serverId;
    public $deploymentEvent;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Site\Site $site
     * @param Server $server
     * @param \App\Models\Site\Deployment\DeploymentEvent $deploymentEvent
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

        $this->deploymentEvent->serverDeployment->update([
            'status' => 'Deployment Failed',
            'failed' => true,
        ]);

        $this->deploymentEvent = $deploymentEvent;
        $this->siteDeploymentId = $deploymentEvent->serverDeployment->siteDeployment->id;
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
        return [
            'deployment_event' => $this->deploymentEvent->load('step'),
            'site_deployment_id' => $this->siteDeploymentId,
        ];
    }
}
