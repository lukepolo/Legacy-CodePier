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
class NewSiteDeployment extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $site;
    public $siteDeployment;

    private $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment)
    {
        $this->user = $site->server->user;

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
