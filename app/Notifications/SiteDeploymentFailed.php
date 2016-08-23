<?php

namespace App\Notifications;

use App\Models\Site;
use App\Models\SiteDeployment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class SiteDeploymentFailed
 * @package App\Notifications
 */
class SiteDeploymentFailed extends Notification
{
    use Queueable;

    public $site;
    public $errorMessage;
    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param Site $site
     * @param SiteDeployment $siteDeployment
     * @param $errorMessage
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment, $errorMessage)
    {
        $this->site = $site;
        $this->errorMessage = $errorMessage;
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
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('('.$this->site->pile->name.') '. $this->site->domain.' Deployment Failed')
            ->line('Your site failed to deploy because : ')
            ->line($this->errorMessage)
            ->action('Go to your site', url('site/'.$this->site->id))
            ->error();

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
            'errorMessage' => $this->errorMessage,
            'siteDeployment' => $this->siteDeployment
        ];
    }
}
