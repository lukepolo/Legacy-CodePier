<?php

namespace App\Notifications\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;

class ServerLoad extends Notification
{
    use Queueable;

    public $server;
    public $slackChannel;

    private $cpus;
    private $highLoad = false;

    /**
     * Create a new notification instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->cpus = $server->stats['cpus'];

        if (($server->stats['loads'][1] / $this->cpus) > .95) {
            $this->highLoad = true;
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
        return $this->highLoad ? $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class], ['broadcast']) : ['broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param Server $server
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($server)
    {
        $mailMessage = (new MailMessage())->subject('High CPU Usage : '.$server->name.' ('.$server->ip.')')->error();

        if ($this->highLoad) {
            $mailMessage = (new MailMessage())->subject('High CPU Usage : '.$server->name.' ('.$server->ip.')')->error();

            foreach ($this->server->stats['loads'] as $mins => $load) {
                $load = round(($load / $this->cpus) * 100, 2);

                $mailMessage
                    ->line($load.'% '.$mins.' minutes ago');
            }

            $mailMessage->line('Across '.$this->cpus.' CPUs');

            return $mailMessage;
        }
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
        $fields = [];
        foreach ($server->stats['loads'] as $mins => $load) {
            $load = round(($load / $this->cpus) * 100, 2);
            $fields[$mins.' minutes ago'] = $load.'%';
        }

        if ($this->highLoad) {
            return (new SlackMessage())
                ->error()
                ->content('High CPU Usage : '.$server->name.' ('.$server->ip.')')
                ->attachment(function ($attachment) use ($server, $fields) {
                    $attachment->title('CPU Allocation across '.$this->cpus.' CPUs')->fields($fields);
                });
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param Server $server
     * @return array
     */
    public function toBroadcast($server)
    {
        return [
            'server'=> $server->id,
            'stats' => $server->stats,
        ];
    }
}
