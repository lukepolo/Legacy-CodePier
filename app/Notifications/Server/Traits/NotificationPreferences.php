<?php namespace App\Notifications\Server\Traits;

use App\Notifications\Channels\DiscordMessageChannel;
use App\Notifications\Channels\SlackMessageChannel;

trait NotificationPreferences
{
    protected function getNotificationPreferences() : array
    {
        return $this
            ->server
            ->user
            ->getNotificationPreferences(
                get_class($this),
                ['mail', SlackMessageChannel::class, DiscordMessageChannel::class],
                ['broadcast']
            );
    }
}
