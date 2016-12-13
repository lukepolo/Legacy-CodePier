<?php

namespace App\Notifications\Site;

use App\Models\Site\Site;
use App\Models\Site\SiteDeployment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\SlackMessageChannel;
use Illuminate\Notifications\Messages\SlackMessage;

class SiteDeploymentSuccessful extends Notification
{
    use Queueable;

    public $site;
    public $siteDeployment;

    /**
     * Create a new notification instance.
     *
     * @param Site $site
     * @param SiteDeployment $siteDeployment
     */
    public function __construct(Site $site, SiteDeployment $siteDeployment)
    {
        $this->site = $site;
        $this->siteDeployment = $siteDeployment;
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
    public function toArray($notifiable)
    {
        return [
            'site' => strip_relations($this->site),
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
        $url = url('site/'.$this->site->id);
        $site = $this->site;
        $siteDeployment = SiteDeployment::findOrFail($this->siteDeployment->id);

        $repositoryProvider = $this->site->userRepositoryProvider->repositoryProvider;

        return (new SlackMessage())
            ->success()
            ->content('Deployment Completed')
            ->attachment(function ($attachment) use ($url, $site, $siteDeployment, $repositoryProvider) {
                $attachment->title('('.$site->pile->name.') '.$site->domain, $url)
                    ->fields([
                        'Commit' => 'https://'.$repositoryProvider->url.'/'.$site->repository.'/'.$repositoryProvider->commit_url.'/'.$siteDeployment->git_commit,
                        'Message' =>  $siteDeployment->commit_message
                    ]);
            });
    }
}
