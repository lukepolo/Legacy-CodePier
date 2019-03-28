<?php

namespace App\Notifications\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Messages\DiscordMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Notifications\Channels\DiscordMessageChannel;

class ServerStatBackToNormal extends Notification
{
    use Queueable;

    public $deliveryMethods;
    public $message;
    public $server;
    public $slackChannel;

    /**
     * Create a new notification instance.
     *
     * @param Server $server
     * @param string $message
     * @param array $deliveryMethods
     */
    public function __construct(Server $server, string $message, array $deliveryMethods = [])
    {
        $this->server = $server;
        $this->message = $message;
        $this->deliveryMethods = $deliveryMethods;

        if ($server->site) {
            $this->slackChannel = $server->site->getSlackChannelName('servers');
        }

        if (empty($this->slackChannel)) {
            $this->slackChannel = $server->name;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return $this->deliveryMethods
            ?: $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return (new MailMessage)
            ->subject($this->getTitle())
            ->line($this->getContent());

    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param Server $server
     *
     * @return SlackMessage
     */
    public function toSlack($server)
    {
        return (new SlackMessage())
            ->success()
            ->content($this->getContent($server))
            ->attachment(function ($attachment) use ($server) {
                $attachment->title($this->getTitle());
            });
    }


    /**
     * Get the Discord representation of the notification.
     *
     * @param Server $server
     *
     * @return DiscordMessage
     */
    public function toDiscord($server)
    {
        return (new DiscordMessage())
            ->success()
            ->content($this->getContent())
            ->embed(function ($embed) use ($server) {
                $embed->title($this->getTitle());
            });
    }

    private function getTitle()
    {
        $server = $this->server;
        return "[Relieved] $this->message : $server->name ($server->ip)";
    }

    private function getContent()
    {
        $server = $this->server;
        return "$this->message back to normal for $server->name ($server->ip).";
    }
}
