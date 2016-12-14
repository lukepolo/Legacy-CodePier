<?php

namespace App\Notifications\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ServerProvisioned extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $server
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(Server $server)
    {
        return (new MailMessage())
            ->line($server->name.' ('.$server->ip.') has been provisioned.')
            ->line('Here is your Root and Mysql Password.')
            ->line('SUDO Password : '.$server->sudo_password)
            ->line('Database Password : '.$server->database_password)
            ->line('Thank you for using our application!');
    }
}
