<?php

namespace App\Events\Server\Site\DeploymentEvents;

use App\Events\Event;
use App\Models\SiteDeployment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class RepositoryClone
 * @package App\Events\Server\Site\DeploymentEvents
 */
class RepositoryCloned extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $siteDeployment;

    /**
     * Create a new event instance.
     */
    public function __construct(SiteDeployment $siteDeployment)
    {

        $siteDeployment->repository_cloned = 1;
        $siteDeployment->save();

        $this->siteDeployment = $siteDeployment;
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
