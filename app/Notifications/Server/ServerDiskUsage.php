<?php namespace App\Notifications\Server;

use App\Models\Server\Server;
use App\Notifications\Channels\DiscordMessageChannel;
use App\Notifications\Channels\SlackMessageChannel;
use App\Notifications\Messages\DiscordMessage;
use App\Notifications\Server\Traits\NotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ServerDiskUsage extends Notification
{
    use NotificationPreferences;
    use Queueable;

    public $server;
    public $slackChannel;

    private $currentNotificationCount;
    private $disk;
    private $diskName;
    private $highLoad = false;

    /**
     * Create a new notification instance.
     *
     * @param Server $server
     * @param $diskName
     */
    public function __construct(Server $server, $diskName)
    {
        $this->server = $server;
        $this->diskName = $diskName;

        $this->disk = last($server->stats->disk_stats[$this->diskName]);
        unset($this->disk['updated_at']);

        $this->currentNotificationCount = isset($this->server->stats->disk_notification_count[$this->diskName]) ? $this->server->stats->disk_notification_count[$this->diskName] : 0;

        if (str_replace('%', '', $this->disk['percent']) >= 95) {
            ++$this->currentNotificationCount;
            if ($this->currentNotificationCount <= 3) {
                $this->highLoad = true;
                $this->server->stats->update([
                    "disk_notification_count" => [
                        $this->diskName => $this->currentNotificationCount
                    ]
                ]);
            }
        } else {
            if ($this->currentNotificationCount >= 3) {
                $server->notify(new ServerStatBackToNormal($server, 'Disk Usage', $this->getNotificationPreferences()));
            }
            $this->server->stats->update([
                "disk_notification_count" => [
                    $this->diskName => 0
                ]
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
            ? $this->getNotificationPreferences()
            : ['broadcast'];
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
        $mailMessage = (new MailMessage())->subject($this->getContent($server))->error();

        $mailMessage->line($this->diskName.': '.$this->getUsedStat());

        return $mailMessage;
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

        return (new SlackMessage())
            ->error()
            ->content($this->getContent($server))
            ->attachment(function ($attachment) use ($server) {
                $attachment = $attachment->title($this->getTitle());
                $attachment->fields([
                    $this->diskName => $this->getUsedStat(),
                ]);
            });
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

        return (new DiscordMessage())
            ->error()
            ->content($this->getContent($server))
            ->embed(function ($embed) use ($server) {
                $embed->title($this->getTitle());
                $embed->field($this->diskName, $this->getUsedStat());
            });
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
            'stats' => $notifiable->stats,
        ];
    }

    private function getContent(Server $server)
    {
        return ($this->currentNotificationCount >= 3 ? '[LAST WARNING] ' : '').'High Disk Usage : '.$server->name.' ('.$server->ip.')';
    }

    private function getTitle()
    {
        return ($this->currentNotificationCount >= 3 ? '[LAST WARNING] ' : '').'Disk Information';
    }

    private function getUsedStat()
    {
        $used = mb_to_readable_format($this->disk['used']);
        $total = mb_to_readable_format($this->disk['used'] + $this->disk['available']);
        return "{$used} / {$total} ({$this->disk['percent']})";
    }
}
