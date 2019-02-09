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
        $this->memory = [];
        $this->server = $server;

        foreach ($server->stats->memory_stats as $memoryName => $stats) {
            $stat = last($stats);
            if (
                is_numeric($stat['available']) &&
                (is_numeric($stat['available']) && $stat['total'] > 0)
            ) {
                if (($stat['available'] / $stat['total']) * 100 <= 5) {
                    $this->memory[$memoryName] = $stat;
                }
            }
        }

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
                    $fields = [];
                    foreach ($memory as $name => $stats) {
                        $fields[$name] = $this->getUsedStat($stats);
                    }
                    $attachment->fields($fields);
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
        $used = mb_to_readable_format($stats['total'] - $stats['available']);
        $total = mb_to_readable_format($stats['total']);
        return "{$used} / {$total} (".round(100 - ($stats['available'] / $stats['total']) * 100).'%)';
    }
}
