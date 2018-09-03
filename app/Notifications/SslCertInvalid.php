<?php

namespace App\Notifications;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use Spatie\SslCertificate\SslCertificate;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Messages\DiscordMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Notifications\Channels\SlackMessageChannel;
use App\Notifications\Channels\DiscordMessageChannel;

class SslCertInvalid extends Notification implements ShouldQueue
{
    use Queueable;

    private $site;
    private $sslCertificate;

    /**
     * Create a new notification instance.
     *
     * @param SslCertificate $sslCertificate
     */
    public function __construct(SslCertificate $sslCertificate = null)
    {
        $this->sslCertificate = $sslCertificate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  Site  $site
     * @return array
     */
    public function via(Site $site)
    {
        $this->site = $site;
        return $site->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return  (new MailMessage)
            ->subject($this->getTitle())
            ->line($this->getContent());
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @return SlackMessage
     */
    public function toSlack()
    {
        return (new SlackMessage())
            ->error()
            ->content($this->getContent());
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @return DiscordMessage
     */
    public function toDiscord()
    {
        return (new DiscordMessage())
            ->error()
            ->embed(function ($embed) {
                $embed->title($this->getContent());
            });
    }

    private function getTitle()
    {
        return $this->site->name.' SSL Certificate is Invalid';
    }

    private function getContent()
    {
        return 'Your sites SSL certificate for '.$this->site->name.' is invalid, please go to CodePier to create a new certificate.';
    }
}
