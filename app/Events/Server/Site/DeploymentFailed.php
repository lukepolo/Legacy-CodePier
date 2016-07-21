<?php

namespace App\Events\Server\Site;

use App\Events\Event;
use App\Models\Site;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class ServerCreated
 * @package App\Events\Server
 */
class DeploymentFailed extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $event;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site, $data)
    {
        $this->event = \App\Models\Event::create([
            'event_id' => $site->id,
            'event_type' => Site::class,
            'description' => 'Deployment Failed',
            'data' => $data,
            'internal_type' => 'provision_status'
        ]);
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [
            url('/')
        ];
    }
}
