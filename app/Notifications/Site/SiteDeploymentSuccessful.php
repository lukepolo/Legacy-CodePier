<?php

namespace App\Notifications\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
use Illuminate\Notifications\Notification;
use App\Notifications\Messages\DiscordMessage;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Notifications\Channels\DiscordMessageChannel;

class SiteDeploymentSuccessful extends Notification
{
    use Queueable;

    public $slackChannel;
    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param SiteDeployment $siteDeployment
     */
    public function __construct(SiteDeployment $siteDeployment)
    {
        $this->siteDeployment = $siteDeployment;
        $this->slackChannel = $siteDeployment->site->getSlackChannelName('site');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return $this->siteDeployment->site->user->getNotificationPreferences(get_class($this), [SlackMessageChannel::class, DiscordMessageChannel::class], ['broadcast']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return [
            'site' => strip_relations($notifiable),
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $commit = '';
        $site = $notifiable;
        $url = url('site/'.$notifiable->id);
        $siteDeployment = SiteDeployment::findOrFail($this->siteDeployment->id);

        if (! empty($notifiable->userRepositoryProvider)) {
            $repositoryProvider = $notifiable->userRepositoryProvider->repositoryProvider;
            if (! empty($repositoryProvider)) {
                $commit = 'https://'.$repositoryProvider->url.'/'.$site->repository.'/'.$repositoryProvider->commit_url.'/'.$siteDeployment->git_commit;
            }
        }

        return (new SlackMessage())
            ->success()
            ->content($this->getContentMessage())
            ->attachment(function ($attachment) use ($url, $commit, $site, $siteDeployment) {
                $attachment->title($this->getTitle($site), $url)
                    ->fields(
                        array_filter([
                            'Commit' => $commit,
                            'Message' =>  $siteDeployment->commit_message,
                        ])
                    );
            });
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $commit = '';
        $site = $notifiable;
        $url = url('site/'.$notifiable->id);
        $siteDeployment = SiteDeployment::findOrFail($this->siteDeployment->id);

        if (! empty($notifiable->userRepositoryProvider)) {
            $repositoryProvider = $notifiable->userRepositoryProvider->repositoryProvider;
            if (! empty($repositoryProvider)) {
                $commit = 'https://'.$repositoryProvider->url.'/'.$site->repository.'/'.$repositoryProvider->commit_url.'/'.$siteDeployment->git_commit;
            }
        }

        return (new DiscordMessage())
            ->success()
            ->content($this->getContentMessage())
            ->embed(function ($embed) use ($url, $commit, $site, $siteDeployment) {
                $embed->title($this->getTitle($site), $url)
                    ->field('Commit', $commit)
                    ->field('Message', $siteDeployment->commit_message);
            });
    }

    private function getContentMessage()
    {
        return 'Deployment Completed';
    }

    private function getTitle(Site $site)
    {
        return '('.$site->pile->name.') '.$site->domain;
    }
}
