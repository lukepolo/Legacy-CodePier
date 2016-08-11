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
class DeploymentStepCompleted implements ShouldBroadcastNow
{
    use SerializesModels;

    public $deploymentEvent;

    private $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site, DeploymentEvent $deploymentEvent, $log, $runtime)
    {
        $this->user = $site->server->user;

        $deploymentEvent->log = $log;
        $deploymentEvent->completed = true;
        $deploymentEvent->runtime = $runtime;
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
        return ['user.'.$this->user->id];
    }
}
