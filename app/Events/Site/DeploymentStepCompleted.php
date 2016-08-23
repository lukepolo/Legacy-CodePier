<?php

namespace App\Events\Site;

use App\Models\DeploymentEvent;
use App\Models\DeploymentStep;
use App\Models\Site;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class ServerCreated
 * @package App\Events\Server
 */
class DeploymentStepCompleted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $step;
    public $deploymentEvent;

    private $siteId;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param DeploymentEvent $deploymentEvent
     * @param $log
     * @param $runtime
     */
    public function __construct(Site $site, DeploymentEvent $deploymentEvent, DeploymentStep $deploymentStep, $log, $runtime)
    {
        $this->siteId = $site->id;

        $deploymentEvent->log = $log;
        $deploymentEvent->completed = true;
        $deploymentEvent->runtime = $runtime;
        $deploymentEvent->save();

        $this->deploymentEvent = $deploymentEvent;
        $this->step = $deploymentStep->step;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Site.' . $this->siteId);
    }
}
