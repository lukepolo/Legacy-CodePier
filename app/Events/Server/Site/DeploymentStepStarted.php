<?php

namespace App\Events\Server\Site;

use App\Models\DeploymentEvent;
use App\Models\Site;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class ServerCreated
 * @package App\Events\Server
 */
class DeploymentStepStarted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $deploymentEvent;

    private $siteID;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site, DeploymentEvent $deploymentEvent)
    {
        $this->siteID = $site->id;

        $deploymentEvent->started = true;
        $deploymentEvent->save();

        $this->deploymentEvent = $deploymentEvent;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Site.' . $this->siteID);
    }
}
