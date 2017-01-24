<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BuoyInstall extends Notification
{
    use Queueable;

    private $lines;
    private $subject;

    /**
     * Create a new notification instance.
     *
     * @param $subject
     * @param $lines
     */
    public function __construct($subject, $lines)
    {
        $this->lines = $lines;
        $this->subject = $subject;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = new MailMessage;
        $mail->subject($this->subject);
        foreach ($this->lines as $textDescription => $text) {
            $mail->line((! empty($textDescription) ? $textDescription.': ' : '').$text);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
