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

class DeploymentStepStarted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    private $siteId;
    private $siteDeployment;
    private $deploymentEvent;
    private $serverDeployment;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param Server $server
     * @param DeploymentEvent $deploymentEvent
     * @param DeploymentStep $deploymentStep
     */
    public function __construct(Site $site, Server $server, DeploymentEvent $deploymentEvent, DeploymentStep $deploymentStep)
    {
        $this->siteId = $site->id;

        $deploymentEvent->update([
            'started' => true,
        ]);

        $deploymentEvent->serverDeployment->update([
            'started' => true,
            'status' => $deploymentStep->step,
        ]);

        $this->deploymentEvent = $deploymentEvent;
        $this->serverDeployment = $deploymentEvent->serverDeployment;
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
        return [
            'site_deployment' => strip_relations($this->siteDeployment),
            'server_deployment' => strip_relations($this->serverDeployment),
            'deployment_event' => strip_relations($this->deploymentEvent)->load('step'),
        ];
    }
}
