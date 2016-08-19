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
class NewSiteDeployment implements ShouldBroadcastNow
{
    use SerializesModels;

    public $site;
    public $siteDeployment;

    private $user;

    /**
     * Create a new event instance.
     * @param Site $site
     * @param SiteDeployment $siteDeployment
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment)
    {
        $this->user = $site->pile->user;

        $this->site = $site;

        $this->siteDeployment = $siteDeployment;
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
