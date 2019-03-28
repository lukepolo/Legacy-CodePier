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
    private $latestLoadStat;
    private $highLoad = false;
    private $currentNotificationCount;
    /**
     * Create a new notification instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->cpus = $server->stats->number_of_cpus;
        $this->latestLoadStat = last($server->stats->load_stats);
        unset($this->latestLoadStat['updated_at']);
        $this->currentNotificationCount = $this->server->stats->load_notification_count;

        if (($this->latestLoadStat[5] / $this->cpus) > .95) {
            ++$this->currentNotificationCount;

            if ($this->currentNotificationCount <= 3) {
                $this->highLoad = true;
                $this->server->stats->update([
                    'load_notification_count' => $this->currentNotificationCount
                ]);
            }
        } elseif ($this->currentNotificationCount !== 0) {
            $notificationPreferences = $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class], ['broadcast']);

            if ($this->currentNotificationCount >= 3) {
                $server->notify(new ServerStatBackToNormal($server, 'CPU Usage', $notificationPreferences));
            }

            $this->server->stats->update([
                'load_notification_count' => 0
            ]);
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
        return $this->highLoad
            ? $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class], ['broadcast'])
            : ['broadcast'];
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
        $mailMessage = (new MailMessage())->subject($this->getContent($server))->error();

        $mailMessage->line($this->getTitle());

        foreach ($this->latestLoadStat as $mins => $load) {
            $mailMessage->line("{$this->calculateLoad($load)} {$this->minsAgo($mins)}");
        }

        return $mailMessage;
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
            ->error()
            ->content($this->getContent($server))
            ->attachment(function ($attachment) use ($server) {
                $fields = [];
                foreach ($this->latestLoadStat as $mins => $load) {
                    $fields[$this->minsAgo($mins)] = $this->calculateLoad($load);
                }

                $attachment->title($this->getTitle())->fields($fields);
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
            ->error()
            ->content($this->getContent($server))
            ->embed(function ($embed) use ($server) {
                $embed->title($this->getTitle());
                foreach ($this->latestLoadStat as $mins => $load) {
                    $embed->field($this->minsAgo($mins), $this->calculateLoad($load));
                }
            });
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
            'stats' => $server->stats,
        ];
    }

    private function getTitle()
    {
        return ($this->currentNotificationCount >= 3 ? '[LAST WARNING] ' : '')."CPU Allocation across $this->cpus CPUs";
    }

    private function getContent(Server $server)
    {
        return ($this->currentNotificationCount >= 3 ? '[LAST WARNING] ' : '')."High CPU Usage : $server->name ($server->ip)";
    }

    private function calculateLoad($load)
    {
        return round(($load / $this->cpus) * 100, 2).'%';
    }

    private function minsAgo($mins)
    {
        return "$mins minute".($mins > 1 ? 's' : '')." ago";
    }
}
