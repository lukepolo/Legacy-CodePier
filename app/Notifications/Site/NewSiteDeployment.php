<?php

namespace App\Notifications\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
use Illuminate\Notifications\Notification;

class NewSiteDeployment extends Notification
{
    use Queueable;

    public $site;
    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param Site           $site
     * @param \App\Models\Site\SiteDeployment $siteDeployment
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment)
    {
        $this->site = $site;
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
    public function toArray($notifiable)
    {
        return [
            'siteDeployment' => $this->siteDeployment->load(['serverDeployments.server', 'serverDeployments.events.step' => function ($query) {
                $query->withTrashed();
            }, 'site.pile', 'site.userRepositoryProvider.repositoryProvider']),
        ];
    }
}
