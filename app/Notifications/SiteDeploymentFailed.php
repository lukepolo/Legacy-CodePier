<?php

namespace App\Notifications;

use App\Models\Site\Site;
use App\Models\Site\SiteDeployment;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SiteDeploymentFailed extends Notification
{
    use Queueable;

    public $site;
    public $domain;
    public $pile;
    public $errorMessage;
    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Site\Site           $site
     * @param SiteDeployment $siteDeployment
     * @param $errorMessage
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment, $errorMessage)
    {
        $this->site = $site;
        $this->pile = $this->site->pile->name;
        $this->domain = $this->site->domain;
        $this->errorMessage = $errorMessage;
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
        return ['mail', 'database', 'broadcast', SlackMessageChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('('.$this->pile.') '.$this->domain.' Deployment Failed')
            ->line('Your site failed to deploy because : ')
            ->line($this->errorMessage)
            ->action('Go to your site', url('site/'.$this->site->id))
            ->error();
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
            'errorMessage'   => $this->errorMessage,
            'siteDeployment' => $this->siteDeployment,
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
        $pile = $this->pile;
        $domain = $this->domain;
        $error = $this->errorMessage;
        $url = url('site/'.$this->site->id);

        return (new SlackMessage())
            ->error()
            ->content('Deployment Failed')
            ->attachment(function ($attachment) use ($url, $error, $pile, $domain) {
                $attachment->title('('.$pile.') '.$domain, $url)
                    ->fields([
                        'Error' => $error,
                    ]);
            });
    }
}
