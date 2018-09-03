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
        return $this->highLoad ? $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class], ['broadcast']) : ['broadcast'];
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
        if ($this->highLoad) {
            $mailMessage = (new MailMessage())->subject($this->getContent($server))->error();

            foreach ($this->server->stats['loads'] as $mins => $load) {
                $mailMessage->line(`{$this->calculateLoad($load)} {$this->minsAgo($mins)}`);
            }

            $mailMessage->line($this->getTitle());

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
        if ($this->highLoad) {
            return (new SlackMessage())
                ->error()
                ->content($this->getContent($server))
                ->attachment(function ($attachment) use ($server) {
                    $fields = [];
                    foreach ($server->stats['loads'] as $mins => $load) {
                        $fields[$this->minsAgo($mins)] = $this->calculateLoad($load);
                    }

                    $attachment->title($this->getTitle())->fields($fields);
                });
        }
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
        if ($this->highLoad) {
            return (new DiscordMessage())
                ->error()
                ->content($this->getContent($server))
                ->embed(function ($embed) use ($server) {
                    $embed->title($this->getTitle());
                    foreach ($server->stats['loads'] as $mins => $load) {
                        $embed->field($this->minsAgo($mins), $this->calculateLoad($load));
                    }
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

    private function getTitle()
    {
        return 'CPU Allocation across '.$this->cpus.' CPUs';
    }

    private function getContent(Server $server)
    {
        return 'High CPU Usage : '.$server->name.' ('.$server->ip.')';
    }

    private function calculateLoad($load)
    {
        return round(($load / $this->cpus) * 100, 2).'%';
    }

    private function minsAgo($mins)
    {
        return $mins.' minutes ago';
    }
}
