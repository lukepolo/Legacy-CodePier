<?php

namespace App\Console\Commands;

use App\Models\Site\Site;
use App\Models\User\User;
use App\Models\Server\Server;
use Illuminate\Console\Command;
use App\Jobs\Server\CreateServer;
use App\Services\RemoteTaskService;
use Symfony\Component\Process\Process;
use App\Services\Site\SiteFeatureService;
use App\Models\Server\Provider\ServerProvider;

class ProvisionDevEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:provision';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Spawns and provisions a development Ubuntu CodePier-provisioned server.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(RemoteTaskService $remoteTaskService, SiteFeatureService $siteFeatureService)
    {
        $this->remoteTaskService = $remoteTaskService;

        $userId = $this->ask('What user ID would you like to use?');

        if ($userId && empty(User::where('id', $userId)->first())) {
            throw new \Exception('It appears as though the you provided does not exist!');
        }

        $site = Site::firstOrCreate([
            'user_id'             => $userId,
            'domain'              => 'default',
            'pile_id'             => 1,
            'name'                => 'default',
        ]);

        $site->update([
            'type'                        => 'PHP',
            'branch'                      => 'master',
            'framework'                   => 'PHP.Laravel',
            'repository'                  => 'cachethq/cachet',
            'web_directory'               => 'public',
            'keep_releases'               => 10,
            'wildcard_domain'             => false,
            'zerotime_deployment'         => true,
            'user_repository_provider_id' => 1,
        ]);

        $server = Server::firstOrCreate([
            'user_id' => 1,
            'name' => 'Dev Server',
            'server_provider_id' => 2,
            'port' =>  22,
            'server_features' => $siteFeatureService->getSuggestedFeatures($site),
            'pile_id' => 1,
            'system_class' => 'ubuntu 16.04',
            'ip' => '192.168.10.10',
        ]);

        if (empty($server->public_ssh_key) || empty($server->private_ssh_key)) {
            $sshKey = $this->remoteTaskService->createSshKey();

            $server->public_ssh_key = $sshKey['publickey'];
            $server->private_ssh_key = $sshKey['privatekey'];
            $server->save();
        }
        \Storage::disk('dev-env')->put('id_rsa.pub', $server->public_ssh_key);

        $process = new Process('cd storage/dev-environment && vagrant up --provision');
        $process->setTimeout(0);
        $process->run();

        return dispatch((new CreateServer(ServerProvider::findOrFail(2), $server))->onQueue(config('queue.channels.server_provisioning')));
    }
}
