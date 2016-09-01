<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Models\Server;
use App\Models\ServerProvisionStep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProvisionServer
 * @package App\Jobs
 */
class ProvisionServer implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    /**
     * Create a new job instance.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function handle(ServerService $serverService)
    {
        if(!$this->server->provisionSteps->count()) {
            $this->createProvisionSteps($this->server);
            $this->server->load('provisionSteps');
        }

        if($serverService->provision($this->server)) {
            foreach ($this->server->user->sshKeys as $sshKey) {
                $serverService->installSshKey($this->server, $sshKey->ssh_key);
            }

            event(new ServerProvisionStatusChanged($this->server, 'Provisioned', 100));
        }
    }

    private function createProvisionSteps(Server $server)
    {
        $coreSteps = collect([
            'OsService' => [
                'updateSystem' => 'Updating the system',
                'setTimezoneToUTC' => 'Settings Timezone to UTC',
                'setLocaleToUTF8' => 'Settings Locale to UTF8',
                'createSwap' => 'Creating Swap',
                'addCodePierUser' => 'Adding CodePier User'
            ],
            'FirewallService' => [
                'installFirewallRules' => 'Installing Basic Firewall Rules'
            ],
            'RepositoryService' => [
                'installGit' => 'Installing GIT'
            ],
            'WebService' => [
                'installNginx' => 'Installing Nginx'
            ],
            'MonitoringService' => [
                'addDiskMonitoringScript' => 'Installing disk monitor script'
            ]
        ]);

        // TODO - make customizable
        $customizableSteps = collect([
            'DatabaseService' => [
                'installDatabases' => 'Installing Database',
                'installRedis' => 'Installing Redis',
                'installMemcached' => 'Installing Memcached'
            ],
            'DaemonService' => [
                'installSupervisor' => 'Installing Supervisor',
                'installBeanstalk' => 'Installing Beanstalk'
            ],
            'WebService' => [
                'installCertBot' => 'Installing LetsEncrypt - Cert Bot'
            ]
        ]);

        // TODO - this is language specific
        $selectedLanguages = ['PHP'];

        $languages = collect([
            'PHP' => collect([
                'installPHP' => 'Installing PHP',
                'installPhpFpm' => 'Installing PHP-FPM',
                'installComposer' => 'Installing Composer',
                'frameworks' => [
                    'Laravel' => [
                        'installEnvoy' => 'Installing Envoy',
                    ]
                ]
            ])
        ]);

        foreach ($coreSteps->merge($customizableSteps) as $service => $serviceFunctions) {
            foreach($serviceFunctions as $function => $step) {
                $this->createStep($server, $service, $function, $step);
            }
        }

        foreach($selectedLanguages as $languageService) {
            foreach($languages[$languageService]->except('frameworks') as $function => $step) {
                $this->createStep($server, 'Languages\\'.$languageService, $function, $step);
            }

            foreach($languages[$languageService]->only('frameworks') as $frameworks) {
                foreach($frameworks as $frameworkService => $serviceFunctions) {
                    foreach($serviceFunctions as $function => $step) {
                        $this->createStep($server, 'Languages\\Frameworks\\'.$frameworkService, $function, $step);
                    }
                }
            }
        }
    }

    private function createStep(Server $server, $service, $function, $step)
    {
        ServerProvisionStep::create([
            'server_id' => $server->id,
            'service' => $service,
            'function' => $function,
            'step' => $step,
        ]);
    }
}
