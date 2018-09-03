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

class ServerMemory extends Notification
{
    use Queueable;

    public $server;
    public $slackChannels;

    private $memory;

    /**
     * Create a new notification instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->memory = [];

        foreach ($server->stats['memory'] as $memoryName => $stats) {
            if (preg_replace('/[^0-9]/', '', $stats['free']) <= 0.2) {
                $this->memory[$memoryName] = $stats;
            }
        }

        $this->slackChannel = $server->name;

        if ($server->site) {
            $this->slackChannel = $server->site->getSlackChannelName('servers');
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ! empty($this->memory) ? $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class], ['broadcast']) : ['broadcast'];
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
        $server = $notifiable;
        $memory = $this->memory;

        if (! empty($memory)) {
            $mailMessage = (new MailMessage())->subject($this->getContent($server))->error();

            foreach ($memory as $name => $stats) {
                $mailMessage->line($name.': '.$this->getUsedStat($stats));
            }

            return $mailMessage;
        }
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
        $server = $notifiable;
        $memory = $this->memory;

        if (! empty($memory)) {
            return (new SlackMessage())
                ->error()
                ->content($this->getContent($server))
                ->attachment(function ($attachment) use ($server, $memory) {
                    $attachment = $attachment->title($this->getTitle());
                    foreach ($memory as $name => $stats) {
                        $attachment->fields([
                            $name => $this->getUsedStat($stats),
                        ]);
                    }
                });
        }
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $server = $notifiable;
        $memory = $this->memory;

        if (! empty($memory)) {
            return (new DiscordMessage())
                ->error()
                ->content($this->getContent($server))
                ->embed(function ($embed) use ($server, $memory) {
                    $embed->title($this->getTitle());
                    foreach ($memory as $name => $stats) {
                        $embed->field($name, $this->getUsedStat($stats));
                    }
                });
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return [
            'server'=> $notifiable->id,
            'stats' => $notifiable->stats,
        ];
    }

    private function getContent(Server $server)
    {
        return 'Memory High : '.$server->name.' ('.$server->ip.')';
    }

    private function getTitle()
    {
        return 'Memory Allocation';
    }

    private function getUsedStat($stats)
    {
        return $stats['used'].' / '.$stats['total'];
    }
}
