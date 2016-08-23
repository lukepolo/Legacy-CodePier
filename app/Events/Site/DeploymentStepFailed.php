<?php

namespace App\Events\Site;

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
class DeploymentStepFailed implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $deploymentEvent;

    private $siteId;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param DeploymentEvent $deploymentEvent
     * @param $log
     */
    public function __construct(Site $site, DeploymentEvent $deploymentEvent, $log)
    {
        $this->siteId = $site->id;

        $deploymentEvent->log = $log;
        $deploymentEvent->completed = true;
        $deploymentEvent->failed = true;
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
