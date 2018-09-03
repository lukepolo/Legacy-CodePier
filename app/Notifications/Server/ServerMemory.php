<?php

namespace App\Notifications\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\DiscordMessage;
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
            $mailMessage = (new MailMessage())->subject('Memory High : '.$server->name.' ('.$server->ip.')')->error();

            foreach ($memory as $name => $stats) {
                $mailMessage->line($name.': '.$stats['used'].' / '.$stats['total']);
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
                ->content('Memory High : '.$server->name.' ('.$server->ip.')')
                ->attachment(function ($attachment) use ($server, $memory) {
                    $attachment = $attachment->title('Memory Allocation');
                    foreach ($memory as $name => $stats) {
                        $attachment->fields([
                            $name => $stats['used'].' / '.$stats['total'],
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
                ->content('Memory High : '.$server->name.' ('.$server->ip.')')
                ->embed(function ($embed) use ($server, $memory) {
                    $embed->title('Memory Allocation');
                    foreach ($memory as $name => $stats) {
                        $embed->field($name, $stats['used'].' / '.$stats['total']);
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
}
