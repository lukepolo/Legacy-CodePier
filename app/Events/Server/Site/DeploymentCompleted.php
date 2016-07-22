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
class DeploymentCompleted extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $event;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site)
    {
        $this->event = \App\Models\Event::create([
            'event_id' => $site->id,
            'event_type' => Site::class,
            'description' => 'Deployment Completed',
            'data' => 'Commit hash here',
            'internal_type' => 'deployment'
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
            '*'
        ];
    }
}
