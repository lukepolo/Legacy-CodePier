<?php

namespace App\Jobs;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UserDataBundle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        dd($this->user->load([
            'sites',
            'sites.cronJobs',
            'sites.deployments',
            'sites.deployments.deploymentEvent',
            'sites.deployments.deploymentStep',
            'sites.deploymentSteps',
            'sites.files',
            'sites.firewallRules',
            'sites.commands',
            'sites.sslCertificates',
            'sites.sshKeys',
            'sites.userRepositoryProvider',
            'sites.workers',
            'sites.daemons',
            'sites.buoys',
            'sites.schemas',
            'sites.schemaUsers',
            'sites.environmentVariables',
            'sites.languageSettings',
            'sites.lifelines',
            'sites.backups',

            'servers',
            'servers.serverProvider',
            'servers.sshKeys',
            'servers.cronJobs',
            'servers.files',
            'servers.firewallRules',
            'servers.workers',
            'servers.daemons',
            'servers.sslCertificates',
            'servers.activeSslCertificates',
            'servers.server_provider_features',
            'servers.provisionSteps',
            'servers.commands',
            'servers.buoys',
            'servers.schemas',
            'servers.schemaUsers',
            'servers.environmentVariables',
            'servers.languageSettings',
            'servers.backups',

            'userServerProviders',
            'userLoginProvider',
            'userRepositoryProviders',
            'userNotificationProviders',
            'sshKeys',
            'piles',
            'notificationSettings',
        ]));
    }
}
