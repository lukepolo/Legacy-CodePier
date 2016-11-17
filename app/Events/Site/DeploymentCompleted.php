<?php

namespace App\Events\Site;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class DeploymentCompleted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $siteId;
    public $serverId;
    public $siteServerDeployment;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param Server $server
     * @param SiteServerDeployment $siteServerDeploymentDeployment
     */
    public function __construct(Site $site, Server $server, SiteServerDeployment $siteServerDeploymentDeployment)
    {
        $this->siteId = $site->id;
        $this->serverId = $server->id;

        $siteServerDeploymentDeployment->update([
            'status' =>  'Deployment Completed',
            'completed' => true,
        ]);

        $this->siteServerDeployment = $siteServerDeploymentDeployment;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.Site.'.$this->siteId);
    }
}
