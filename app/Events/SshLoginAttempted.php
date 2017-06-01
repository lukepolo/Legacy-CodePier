<?php

namespace App\Events;

use App\Models\Server\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SshLoginAttempted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $server;

    public $state;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param bool $state
     */
    public function __construct(Collection $server, bool $state)
    {
        $this->server = $server;
        $this->state = $state;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
