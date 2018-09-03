<?php

namespace App\Notifications\Site;

use Illuminate\Bus\Queueable;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Notifications\Notification;
use App\Notifications\Messages\DiscordMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Notifications\Channels\SlackMessageChannel;
use App\Notifications\Channels\DiscordMessageChannel;

class SiteDeploymentFailed extends Notification
{
    use Queueable;

    public $server;
    public $errorMessage;
    public $slackChannel;
    public $siteServerDeployment;

    /**
     * Create a new notification instance.
     *
     * @param SiteServerDeployment $siteServerDeployment
     * @param $errorMessage
     * @internal param SiteDeployment $siteDeployment
     */
    public function __construct(SiteServerDeployment $siteServerDeployment, $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        $this->siteServerDeployment = $siteServerDeployment;
        $this->server = $this->siteServerDeployment->server;

        $this->slackChannel = $this->siteServerDeployment->siteDeployment->site->getSlackChannelName('site');
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
        return $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class]);
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
            ->markdown('mail.notifications.deployment-failed', [
                'errorMessage' => str_replace("\n", '<br>', $this->errorMessage),
            ])
            ->subject('('.$notifiable->pile->name.') '.$notifiable->domain.' Deployment Failed')
            ->line('Your site failed to deploy on '.$this->server->name.' ('.$this->server->ip.') '.' because : ')
            ->action('Go to your site', url('site/'.$notifiable->id))
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
        $pile = $notifiable->pile->name;
        $domain = $notifiable->domain;
        $error = $this->errorMessage;
        $url = url('site/'.$notifiable->id);

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

    /**
     * Get the Discord representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $pile = $notifiable->pile->name;
        $domain = $notifiable->domain;
        $error = strlen($this->errorMessage) >= 1024 ? substr($this->errorMessage, 0, 1021).'...' : $this->errorMessage;
        $url = url('site/'.$notifiable->id);

        return (new DiscordMessage('Deployment Failed'))
            ->error()
            ->embed(function ($embed) use ($url, $error, $pile, $domain) {
                $embed->title('('.$pile.') '.$domain, $url)->field('Error', $error);
            });
    }
}
