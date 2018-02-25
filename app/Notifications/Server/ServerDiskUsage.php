<?php

namespace App\Notifications\Server;

use App\Models\Server\Server;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

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

        foreach ($server->stats['disk_usage'] as $disk => $stats) {
            if (str_replace('%', '', $stats['percent']) >= 95) {
                $this->disks[$disk] = $stats;
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
        return ! empty($this->disks) ? $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class], ['broadcast']) : ['broadcast'];
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
            $mailMessage = (new MailMessage())->subject('High Disk Usage : '.$server->name.' ('.$server->ip.')')->error();

            foreach ($disks as $name => $stats) {
                $mailMessage->line($name.': '.$stats['used'].' / '.$stats['available']);
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
                ->content('High Disk Usage : '.$server->name.' ('.$server->ip.')')
                ->attachment(function ($attachment) use ($server, $disks) {
                    $attachment = $attachment->title('Disk Information');
                    foreach ($disks as $name => $stats) {
                        $attachment->fields([
                            $name => $stats['used'].' / '.$stats['available'],
                        ]);
                    }
                });
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param $notifiable
     *
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
