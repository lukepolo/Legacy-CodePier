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

class ServerDiskUsage extends Notification
{
    use Queueable;

    public $server;
    public $slackChannel;

    private $disks = [];

    /**
     * Create a new notification instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;

        foreach ($server->stats->disk_stats as $disk => $stats) {
            $stat = last($stats);
            unset($stat['updated_at']);
            if (str_replace('%', '', $stat['percent']) >= 95) {
                $this->disks[$disk] = $stat;
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
        return ! empty($this->disks) ? $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class], ['broadcast']) : ['broadcast'];
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
        $disks = $this->disks;

        if (! empty($disks)) {
            $mailMessage = (new MailMessage())->subject($this->getContent($server))->error();

            foreach ($disks as $name => $stats) {
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
        $disks = $this->disks;

        if (! empty($disks)) {
            return (new SlackMessage())
                ->error()
                ->content($this->getContent($server))
                ->attachment(function ($attachment) use ($server, $disks) {
                    $attachment = $attachment->title($this->getTitle());
                    foreach ($disks as $name => $stats) {
                        $attachment->fields([
                            $name => $this->getUsedStat($stats),
                        ]);
                    }
                });
        }
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
        $server = $notifiable;
        $disks = $this->disks;

        if (! empty($disks)) {
            return (new DiscordMessage())
                ->error()
                ->content($this->getContent($server))
                ->embed(function ($embed) use ($server, $disks) {
                    $embed->title($this->getTitle());
                    foreach ($disks as $name => $stats) {
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
        return 'High Disk Usage : '.$server->name.' ('.$server->ip.')';
    }

    private function getTitle()
    {
        return 'Disk Information';
    }

    private function getUsedStat($stats)
    {
        $used = mb_to_readable_format($stats['used']);
        $total = mb_to_readable_format($stats['used'] + $stats['available']);
        return "{$used} / {$total} ({$stats['percent']})";
    }
}
