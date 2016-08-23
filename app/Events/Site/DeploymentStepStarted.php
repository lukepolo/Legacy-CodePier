<?php

namespace App\Events\Site;

use App\Models\DeploymentEvent;
use App\Models\DeploymentStep;
use App\Models\Site;
use Illuminate\Broadcasting\Channel;
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

    public $step;
    public $deploymentEvent;

    private $siteID;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site, DeploymentEvent $deploymentEvent, DeploymentStep $deploymentStep)
    {
        $this->siteID = $site->id;

        $deploymentEvent->started = true;
        $deploymentEvent->save();

        $this->deploymentEvent = $deploymentEvent;
        $this->step = $deploymentStep->step;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.Site.' . $this->siteID);
    }
}
