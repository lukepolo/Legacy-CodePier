<?php

namespace App\Console\Commands;

use App\Models\Site\Site;
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

        $site = Site::create([
            'user_id' => 1,
            'domain' => 'codepier.dev',
            'pile_id' => 1,
            'name' => 'Dev',
            'type' => 'PHP',
            'branch' => 'master',
            'framework' => 'PHP.Laravel',
            'repository' => 'codepier/dev-env',
            'web_directory' => 'public',
            'keep_releases' => 10,
            'wildcard_domain' => false,
            'zero_downtime_deployment' => true,
            'user_repository_provider_id' => 1,
        ]);

        $server = Server::create([
            'user_id' => 1,
            'name' => 'Development Environment',
            'server_provider_id' => ServerProvider::where('provider_name', 'custom')->first()->id,
            'ip' => '192.168.10.10',
            'port' => 22,
            'server_features' => $siteFeatureService->getSuggestedFeatures($site),
            'system_class' => 'ubuntu 16.04',
        ]);

        $server->public_ssh_key = file_get_contents(storage_path('dev-environment/id_rsa.pub'));
        $server->private_ssh_key = file_get_contents(storage_path('dev-environment/id_rsa'));
        $server->save();

        $process = new Process('cd storage/dev-environment && vagrant up --provision');
        $process->setTimeout(0);
        $process->run();

        return dispatch(
            (new CreateServer(ServerProvider::where('provider_name', 'custom')->first(), $server))
                ->onQueue(config('queue.channels.server_provisioning'))
        );
    }
}
