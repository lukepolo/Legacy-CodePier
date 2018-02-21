<?php

namespace App\Notifications;

use App\Models\Site\Lifeline;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;

class LifeLineThresholdExceeded extends Notification implements ShouldQueue
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

        $this->lifeline->increment('sent_notifications');

        $this->slackChannel = $this->lifeline->site->getSlackChannelName('lifelines');

        return $this->lifeline->site->user->getNotificationPreferences(get_class($this), ['mail', SlackMessageChannel::class]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        $mailMessage = (new MailMessage)
            ->subject($this->lifeline->site->name.' lifeline failed to check-in')
            ->line('Your lifeline '.$this->lifeline->name.' for '.$this->lifeline->site->name.' has not checked in since '.$this->lifeline->last_seen.'.');

        if ($this->lifeline->sent_notifications == 3) {
            $mailMessage->line('Last warning! You will receive a notification when a lifeline has been updated');
        }

        return $mailMessage;
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @return SlackMessage
     */
    public function toSlack()
    {
        $message = 'Your lifeline '.$this->lifeline->name.' for '.$this->lifeline->site->name.' has not checked in since '.$this->lifeline->last_seen.'.';

        if ($this->lifeline->sent_notifications == 3) {
            $message .= ' Last warning! You will receive a notification when a lifeline has been updated';
        }

        return (new SlackMessage())
            ->error()
            ->content($message);
    }
}
