<?php

namespace App\Notifications;

use App\Models\Site;
use App\Models\SiteDeployment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Class SiteDeploymentSuccessful
 * @package App\Notifications
 */
class SiteDeploymentSuccessful extends Notification
{
    use Queueable;

    public $site;
    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param Site $site
     * @param SiteDeployment $siteDeployment
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment)
    {
        $this->site = $site;
        $this->siteDeployment = $siteDeployment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'site' => $this->site,
            'siteDeployment' => $this->siteDeployment
        ];
    }
}
