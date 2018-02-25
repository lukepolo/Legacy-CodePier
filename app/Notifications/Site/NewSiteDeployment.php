<?php

namespace App\Notifications\Site;

use App\Models\Site\SiteDeployment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSiteDeployment extends Notification
{
    use Queueable;

    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Site\SiteDeployment $siteDeployment
     */
    public function __construct(SiteDeployment $siteDeployment)
    {
        $this->siteDeployment = $siteDeployment;
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
    public function toBroadcast($notifiable)
    {
        return [
            'siteDeployment' => $this->siteDeployment,
        ];
    }
}
