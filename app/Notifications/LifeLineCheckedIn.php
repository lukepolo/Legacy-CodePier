<?php

namespace App\Notifications;

use function snake_case;
use App\Models\Site\Lifeline;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;

class LifeLineCheckedIn extends Notification implements ShouldQueue
{
    use Queueable;

    public $lifeline;
    public $slackChannel;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $this->lifeline = Lifeline::with('site.user')->findOrFail($notifiable->id);

        $this->slackChannel = snake_case($this->lifeline->site->name);

        return ['mail', SlackMessageChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return  (new MailMessage)
            ->subject($this->lifeline->site->name.' lifeline checked in')
            ->line('Your lifeline '.$this->lifeline->name.' for '.$this->lifeline->site->name.' has checked back in.');

    }

    /**
     * Get the Slack representation of the notification.
     *
     * @return SlackMessage
     */
    public function toSlack()
    {
       return (new SlackMessage())
            ->error()
            ->content('Your lifeline '.$this->lifeline->name.' for '.$this->lifeline->site->name.' has checked back in.');
    }
}
