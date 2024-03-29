<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\Site\Deployment\DeploymentEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class DeploymentStepCompleted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    private $siteId;
    private $siteDeployment;
    private $deploymentEvent;
    private $serverDeployment;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param Server $server
     * @param DeploymentEvent $deploymentEvent
     * @param $log
     * @param $runtime
     */
    public function __construct(Site $site, Server $server, DeploymentEvent $deploymentEvent, $log, $runtime)
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
        strip_relations($this->deploymentEvent)->load('step');

        return [
            'site_deployment' => strip_relations($this->siteDeployment),
            'server_deployment' => strip_relations($this->serverDeployment),
            'deployment_event' => $this->deploymentEvent,
        ];
    }
}
