<?php

namespace App\Notifications\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
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
        return ['broadcast'];
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
            'site'           => $this->site,
        ];
    }
}
