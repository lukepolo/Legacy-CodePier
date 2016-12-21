<?php

namespace App\Notifications\Site;

use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
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
            'siteDeployment' => $this->siteDeployment->load([
                'serverDeployments.events.step' => function ($query) {
                    $query->withTrashed();
                },
            ]),
        ];
    }
}
