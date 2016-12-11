<?php

namespace App\Notifications\Site;

use App\Models\Site\Site;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SiteDeploymentSuccessful extends Notification
{
    use Queueable;

    public $site;
    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param Site           $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', SlackMessageChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'site' => strip_relations($this->site),
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $url = url('site/'.$this->site->id);
        $site = $this->site;

        return (new SlackMessage())
            ->success()
            ->content('Deployment Completed')
            ->attachment(function ($attachment) use ($url, $site) {
                $attachment->title('('.$site->pile->name.') '.$site->domain, $url);
            });
    }
}
