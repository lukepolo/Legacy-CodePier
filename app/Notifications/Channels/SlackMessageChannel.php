<?php

namespace App\Notifications\Channels;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class SlackMessageChannel
 * @package App\Notifications\Channels
 */
class SlackMessageChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Create a new Slack channel instance.
     *
     * @param  \GuzzleHttp\Client $http
     * @return void
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send($notifiable, Notification $notification)
    {
        if ((!$token = $notifiable->routeNotificationFor('slack')) || (!$channel = $notifiable->getSlackChannel())) {
            return;
        }

        $message = $notification->toSlack($notifiable);

       return $this->http->post('https://slack.com/api/chat.postMessage', [
            'form_params' => [
                'token' => $token,
                'channel' => '#'.$channel,
                'text' => $message->content,
                'attachments' => json_encode($this->attachments($message)),
            ],
        ]);
    }

    /**
     * Format the message's attachments.
     *
     * @param  \Illuminate\Notifications\Messages\SlackMessage $message
     * @return array
     */
    protected function attachments(SlackMessage $message)
    {
        return collect($message->attachments)->map(function ($attachment) use ($message) {
            return array_filter([
                'color' => $message->color(),
                'title' => $attachment->title,
                'text' => $attachment->content,
                'title_link' => $attachment->url,
                'fields' => $this->fields($attachment),
            ]);
        })->all();
    }

    /**
     * Format the attachment's fields.
     *
     * @param  \Illuminate\Notifications\Messages\SlackAttachment $attachment
     * @return array
     */
    protected function fields(SlackAttachment $attachment)
    {
        return collect($attachment->fields)->map(function ($value, $key) {
            return ['title' => $key, 'value' => $value, 'short' => true];
        })->values()->all();
    }
}
