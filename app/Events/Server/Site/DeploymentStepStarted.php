<?php

namespace App\Events\Server\Site;

use App\Events\Event;
use App\Models\DeploymentEvent;
use App\Models\Site;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class ServerCreated
 * @package App\Events\Server
 */
class DeploymentStepStarted extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $deploymentEvent;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site, DeploymentEvent $deploymentEvent)
    {
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
        return [
            '*'
        ];
    }
}
