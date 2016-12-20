<?php

namespace App\Notifications\Site;

use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;

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
        $this->slackChannel = 'deployments';
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
        return ['broadcast', SlackMessageChannel::class];
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
        $url = url('site/'.$notifiable->id);
        $site = $notifiable;
        $siteDeployment = SiteDeployment::findOrFail($this->siteDeployment->id);

        $repositoryProvider = $notifiable->userRepositoryProvider->repositoryProvider;

        return (new SlackMessage())
            ->success()
            ->content('Deployment Completed')
            ->attachment(function ($attachment) use ($url, $site, $siteDeployment, $repositoryProvider) {
                $attachment->title('('.$site->pile->name.') '.$site->domain, $url)
                    ->fields([
                        'Commit' => 'https://'.$repositoryProvider->url.'/'.$site->repository.'/'.$repositoryProvider->commit_url.'/'.$siteDeployment->git_commit,
                        'Message' =>  $siteDeployment->commit_message,
                    ]);
            });
    }
}
