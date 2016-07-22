<?php

namespace App\Events\Server\Site\DeploymentEvents;

use App\Events\Event;
use App\Models\SiteDeployment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class FoldersSetup
 * @package App\Events\Server\Site\DeploymentEvents
 */
class FoldersSetup extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $siteDeployment;

    /**
     * Create a new event instance.
     */
    public function __construct(SiteDeployment $siteDeployment)
    {

        $siteDeployment->folders_setup =1;
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
