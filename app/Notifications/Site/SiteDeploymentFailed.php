<?php

namespace App\Notifications\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;

class SiteDeploymentFailed extends Notification
{
    use Queueable;

    public $pile;
    public $site;
    public $domain;
    public $server;
    public $errorMessage;
    public $siteServerDeployment;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Site\Site $site
     * @param SiteServerDeployment $siteServerDeployment
     * @param $errorMessage
     * @internal param SiteDeployment $siteDeployment
     */
    public function __construct(Site $site, SiteServerDeployment $siteServerDeployment, $errorMessage)
    {
        $this->site = $site;
        $this->domain = $this->site->domain;
        $this->errorMessage = $errorMessage;
        $this->pile = $this->site->pile->name;
        $this->siteServerDeployment = $siteServerDeployment;
        $this->server = $this->siteServerDeployment->server;
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
        return ['mail', SlackMessageChannel::class];
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
            ->line('Your site failed to deploy on '.$this->server->name.' ('.$this->server->ip.') '.' because : ')
            ->line($this->errorMessage)
            ->action('Go to your site', url('site/'.$this->site->id))
            ->error();
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
