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
            ->subject($server->name.' ('.$server->ip.') has been provisioned.')
            ->line($server->name.' ('.$server->ip.') has been provisioned.')
            ->line('Here are your passwords:')
            ->line('sudo Password: '.$server->sudo_password)
            ->line('In order to login to your server, make sure that you have added an SSH key to your account.')
            ->line('Then you can run: "ssh codepier@'.$server->ip.'"')
            ->line('Database Password (user: codepier): '.$server->database_password)
            ->line('Thank you for using our application!');
    }
}
