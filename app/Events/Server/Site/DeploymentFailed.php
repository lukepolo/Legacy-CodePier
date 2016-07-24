<?php

namespace App\Events\Server\Site;

use App\Events\Event;
use App\Models\Site;
use App\Models\SiteDeployment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class ServerCreated
 * @package App\Events\Server
 */
class DeploymentFailed extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $event;
    public $siteDeployment;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment, $data)
    {
        $siteDeployment->log = $data;
        $siteDeployment->save();

        $this->event = \App\Models\Event::create([
            'event_id' => $site->id,
            'event_type' => Site::class,
            'description' => 'Deployment Failed',
            'data' => $data,
            'internal_type' => 'deployment'
        ]);

        $user = $site->server->user;

        Mail::raw('Deployment Failed'. $data, function ($message) use($user) {
            $message->to($user->email);
            $message->subject('CodePier Server Provisioned');
        });

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
