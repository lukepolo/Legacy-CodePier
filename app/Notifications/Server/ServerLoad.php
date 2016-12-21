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

    private $load = false;

    /**
     * Create a new notification instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;

        $cpus = $server->stats['cpus'];

        if (($server->stats['loads'][1] / $cpus) > .95) {
            $this->load = true;
        }

        $this->slackChannel = 'servers';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return $this->load ? ['mail', 'broadcast', SlackMessageChannel::class] : ['broadcast'];
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
        $load = $this->load;

        $mailMessage = (new MailMessage())->subject('High CPU Usage : '.$server->name.' ('.$server->ip.')')->error();

        if ($load) {
            $mailMessage = (new MailMessage())->subject('High CPU Usage : '.$server->name.' ('.$server->ip.')')->error();

            foreach ($this->server->stats['loads'] as $mins => $load) {
                $mailMessage
                    ->line($load.'% '.$mins.' minutes ago');
            }

            $mailMessage->line('Across '.$this->server->stats['cpus'].' CPUS');

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
        $load = $this->load;

        $fields = [];
        foreach ($server->stats['loads'] as $mins => $load) {
            $fields[$mins.' minutes ago'] = $load.'%';
        }

        if ($load) {
            return (new SlackMessage())
                ->error()
                ->content('High CPU Usage : '.$server->name.' ('.$server->ip.')')
                ->attachment(function ($attachment) use ($server, $fields) {
                    $attachment->title('CPU Allocation across '.$server->stats['cpus'].' CPUS')->fields($fields);
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
