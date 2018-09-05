<?php

namespace App\Notifications\Channels;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Notification;
use App\Notifications\Messages\DiscordEmbed;
use App\Notifications\Messages\DiscordMessage;

class DiscordMessageChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Create a new Discord channel instance.
     *
     * @param \GuzzleHttp\Client $http
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send($notifiable, Notification $notification)
    {
        $webhook = $notifiable->routeNotificationForDiscord();

        if (! empty($webhook)) {
            $message = $notification->toDiscord($notifiable);

            $response = $this->http->post($webhook, [
                'json' => [
                    'content' => $message->content,
                    'embeds' => $this->embeds($message),
                    'username' => 'CodePier',
                    'avatar_url' => 'https://cdn.codepier.io/assets/img/CP_Logo_SQ-white.png',
                ],
            ]);

            return $response;
        }
    }

    /**
     * Format the message's embeds.
     *
     * @param \App\Notifications\Messages\DiscordMessage $message
     *
     * @return array
     */
    protected function embeds(DiscordMessage $message)
    {
        return collect($message->embeds)->map(function (DiscordEmbed $embed) use ($message) {
            return array_filter([
                'color' => $message->color(),
                'title' => $embed->title,
                'description' => $embed->description,
                'link' => $embed->url,
                'fields' => $embed->fields,
            ]);
        })->all();
    }
}
