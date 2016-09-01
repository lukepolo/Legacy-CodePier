<?php

namespace App\Notifications;

use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class ServerProvisioned.
 */
class ServerProvisioned extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
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
            ->line('Here is your Root and Mysql Password.')
            ->line('SUDO Password : '.decrypt($server->root_password))
            ->line('Database Password : '.decrypt($server->database_password))
            ->line('Thank you for using our application!');
    }
}
