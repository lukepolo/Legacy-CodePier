<?php

namespace App\Events\Site;

use App\Models\Site\Deployment\DeploymentEvent;
use App\Models\Site\Deployment\DeploymentStep;
use App\Models\Site\Site;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class DeploymentStepStarted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $step;
    public $deploymentEvent;

    private $siteId;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param DeploymentEvent $deploymentEvent
     * @param DeploymentStep $deploymentStep
     */
    public function __construct(Site $site, DeploymentEvent $deploymentEvent, DeploymentStep $deploymentStep)
    {
        $this->siteId = $site->id;

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
        return new PrivateChannel('App.Models.Site.'.$this->siteId);
    }
}
