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

class DeploymentStepCompleted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    private $siteId;
    public $siteDeployment;
    public $deploymentEvent;
    public $serverDeployment;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param Server $server
     * @param DeploymentEvent $deploymentEvent
     * @param DeploymentStep $deploymentStep
     * @param $log
     * @param $runtime
     */
    public function __construct(Site $site, Server $server, DeploymentEvent $deploymentEvent, DeploymentStep $deploymentStep, $log, $runtime)
    {
        $this->siteId = $site->id;

        $deploymentEvent->update([
            'log' => $log,
            'completed' => true,
            'runtime' => $runtime,
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
            'site_deployment' => $this->siteDeployment,
            'server_deployment' => $this->serverDeployment,
            'deployment_event' => $this->deploymentEvent->load('step'),
        ];
    }
}
