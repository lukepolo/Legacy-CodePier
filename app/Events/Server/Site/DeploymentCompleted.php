<?php

namespace App\Events\Server\Site;

use App\Events\Event;
use App\Models\Site;
use App\Models\SiteDeployment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class ServerCreated
 * @package App\Events\Server
 */
class DeploymentCompleted extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $event;
    public $siteDeployment;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param SiteDeployment $siteDeployment
     * @param $log
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment, $data, $log)
    {
        $this->event = \App\Models\Event::create([
            'event_id' => $site->id,
            'event_type' => Site::class,
            'description' => 'Deployment Successful',
            'data' => $data,
            'log' => $log,
            'internal_type' => 'deployment'
        ]);

        $siteDeployment->status = 'completed';
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
