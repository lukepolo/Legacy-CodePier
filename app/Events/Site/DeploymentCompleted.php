<?php

namespace App\Events\Site;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Models\Site\SiteDeployment;
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
    public $siteDeployment;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param Server $server
     * @param SiteDeployment $siteDeployment
     */
    public function __construct(Site $site, Server $server, SiteDeployment $siteDeployment)
    {
        $this->siteId = $site->id;
        $this->serverId = $server->id;
        $this->siteDeployment = $siteDeployment;
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
