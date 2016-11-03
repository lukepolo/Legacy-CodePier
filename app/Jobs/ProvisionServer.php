<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Models\Server\Server;
use App\Models\Server\ServerProvisionStep;
use App\Services\Systems\SystemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProvisionServer.
 */
class ProvisionServer implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    protected $server;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Server\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        if (! $this->server->provisionSteps->count()) {
            $this->createProvisionSteps($this->server);
        }

        $this->server->load('provisionSteps');

        if ($serverService->provision($this->server)) {
            $this->server->user->load('sshKeys');
            foreach ($this->server->user->sshKeys as $sshKey) {
                $serverService->installSshKey($this->server, $sshKey->ssh_key);
            }

            foreach ($this->server->sites as $site) {
                $siteService->create($this->server, $site);
            }

            event(new ServerProvisionStatusChanged($this->server, 'Provisioned', 100));
        }
    }

    private function createProvisionSteps(Server $server)
    {
        $provisionSteps = [
            SystemService::SYSTEM => [
                'updateSystem' => [
                    'step' => 'Updating the system',
                ],
                'setTimezoneToUTC' => [
                    'step' => 'Settings Timezone to UTC',
                ],
                'setLocaleToUTF8' => [
                    'step' => 'Settings Locale to UTF8',
                ],
                'addCodePierUser' => [
                    'step' => 'Adding CodePier User',
                ],
            ],
            SystemService::FIREWALL => [
                'addBasicFirewallRules' => [
                    'step' => 'Installing Basic Firewall Rules',
                ],
            ],
            SystemService::REPOSITORY => [],
            SystemService::WEB => [],
            SystemService::MONITORING => [],
        ];

        foreach ($server->server_features as $service => $features) {
            foreach ($features as $function => $feature) {
                if (isset($feature['enabled']) && $feature['enabled']) {
                    $provisionSteps[$service]['install'.$function] = [
                        'step' => 'Installing '.$function,
                        'parameters' => isset($feature['parameters']) ? $feature['parameters'] : [],
                    ];
                }
            }
        }
        foreach ($provisionSteps as $service => $serviceFunctions) {
            foreach ($serviceFunctions as $function => $data) {
                $this->createStep($server, $service, $function, $data['step'], isset($data['parameters']) ? $data['parameters'] : []);
            }
        }
    }

    private function createStep(Server $server, $service, $function, $step, $parameters = [])
    {
        ServerProvisionStep::create([
            'server_id' => $server->id,
            'service' => $service,
            'function' => $function,
            'step' => $step,
            'parameters' => $parameters,
        ]);
    }
}
