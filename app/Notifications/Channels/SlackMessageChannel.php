<?php

namespace App\Notifications\Channels;

use App\Models\SlackChannel;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Notification;
use App\Exceptions\SlackMessageMissingParams;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\SlackAttachment;

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
     * @throws SlackMessageMissingParams
     */
    public function send($notifiable, Notification $notification)
    {
        $slackChannel = snake_case($notification->slackChannel);

        $token = $notifiable->routeNotificationFor('slack');

        if (! empty($token)) {
            if (! $channel = $slackChannel) {
                throw new SlackMessageMissingParams('Notification must have a public $slackChannel set');
            }

            $message = $notification->toSlack($notifiable);

            if (empty($notifiable->slackChannel) || $notifiable->slackChannel->channel != $slackChannel) {

                if($notifiable->slackChannel) {
                    $notifiable->slackChannel()->delete();
                }

                $response = $this->http->post('https://slack.com/api/channels.create', [
                    'form_params' => [
                        'token'    => $token,
                        'name'     => $slackChannel,
                    ],
                ]);

                if ($response->getStatusCode() == 200) {
                    $response = json_decode($response->getBody()->getContents());
                    if ($response->ok || $response->error === 'name_taken') {
                        $slackChannel = SlackChannel::create([
                            'channel' => $slackChannel,
                            'created' => true,
                        ]);

                        $notifiable->slackChannel()->save($slackChannel);
                    } else {
                        \Log::info('Unable to create slack channel', [$response]);
                    }
                }
            }

            $response = $this->http->post('https://slack.com/api/chat.postMessage', [
                'form_params' => [
                    'token'       => $token,
                    'channel'     => '#'.$channel,
                    'text'        => $message->content,
                    'attachments' => json_encode($this->attachments($message)),
                ],
            ]);

            return $response;
        }
    }

    /**
     * Format the message's attachments.
     *
     * @param \Illuminate\Notifications\Messages\SlackMessage $message
     *
     * @return array
     */
    protected function attachments(SlackMessage $message)
    {
        return collect($message->attachments)->map(function ($attachment) use ($message) {
            return array_filter([
                'color'      => $message->color(),
                'title'      => $attachment->title,
                'text'       => $attachment->content,
                'title_link' => $attachment->url,
                'fields'     => $this->fields($attachment),
            ]);
        })->all();
    }

    /**
     * Format the attachment's fields.
     *
     * @param \Illuminate\Notifications\Messages\SlackAttachment $attachment
     *
     * @return array
     */
    protected function fields(SlackAttachment $attachment)
    {
        return collect($attachment->fields)->map(function ($value, $key) {
            return ['title' => $key, 'value' => $value, 'short' => true];
        })->values()->all();
    }
}
