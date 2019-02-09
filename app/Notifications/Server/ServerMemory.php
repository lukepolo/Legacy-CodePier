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
    private $highLoad;
    private $memoryName;
    private $currentNotificationCount;

    /**
     * Create a new notification instance.
     *
     * @param Server $server
     * @param string $memoryName
     */
    public function __construct(Server $server, $memoryName)
    {

        $this->server = $server;
        $this->memoryName = $memoryName;

        $stat = last($server->stats->memory_stats[$this->memoryName]);
        unset($stat['updated_at']);

        $this->memory = $stat;

        $this->currentNotificationCount = $this->server->stats->memory_notification_count[$this->memoryName] ?: 0;

        if (
            is_numeric($stat['available']) &&
            (is_numeric($stat['available']) && $stat['total'] > 0)
        ) {
            if (($stat['available'] / $stat['total']) * 100 <= 5) {
                ++$this->currentNotificationCount;
                if ($this->currentNotificationCount <= 3) {
                    $this->highLoad = true;
                    $this->server->stats->update([
                        "memory_notification_count" => [
                            $this->memoryName => $this->currentNotificationCount
                        ]
                    ]);
                }
            } else {
                $this->server->stats->update([
                    "memory_notification_count" => [
                        $this->memoryName => 0
                    ]
                ]);
                if ($this->currentNotificationCount >= 3) {
                    ddd("SEND NOTFICAITION SASYING WERE ALL GOOD");
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
        return $this->highLoad ? $this->server->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class, DiscordMessageChannel::class], ['broadcast']) : ['broadcast'];
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

            $mailMessage->line($this->memoryName.': '.$this->getUsedStat());

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
                    $fields[$this->memoryName] = $this->getUsedStat();
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
                    $embed->field($this->memoryName, $this->getUsedStat());
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
        return ($this->currentNotificationCount >= 3 ? '[LAST WARNING] ' : '').'Memory High : '.$server->name.' ('.$server->ip.')';
    }

    private function getTitle()
    {
        return ($this->currentNotificationCount >= 3 ? '[LAST WARNING] ' : '').'Memory Allocation';
    }

    private function getUsedStat()
    {
        $used = mb_to_readable_format($this->memory['total'] - $this->memory['available']);
        $total = mb_to_readable_format($this->memory['total']);
        return "{$used} / {$total} (".round(100 - ($this->memory['available'] / $this->memory['total']) * 100).'%)';
    }
}
