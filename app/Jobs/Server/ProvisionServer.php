<?php

namespace App\Jobs\Server;

use App\Jobs\Site\CreateSite;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Server\ServerProvisionStep;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Contracts\Server\ServerServiceContract as ServerService;

class ProvisionServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    protected $server;

    public $tries = 1;
    public $timeout = 900;

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
     */
    public function handle(ServerService $serverService)
    {
        if (! $this->server->provisionSteps->count()) {
            $this->createProvisionSteps($this->server);
        }

        $this->server->load('provisionSteps');

        if ($serverService->provision($this->server)) {
            $this->server->user->load('sshKeys');

            foreach ($this->server->user->sshKeys as $sshKey) {
                dispatch(
                    (new InstallServerSshKey($this->server, $sshKey))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }

            foreach ($this->server->sites as $site) {
                dispatch(
                    (new CreateSite($this->server, $site))
                        ->onQueue(config('queue.channels.server_commands'))
                );
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
                'setupUnattendedSecurityUpgrades' => [
                    'step' => 'Setup Unattended Security Upgrades',
                ],
            ],
            SystemService::FIREWALL => [
                'addBasicFirewallRules' => [
                    'step' => 'Installing Basic Firewall Rules',
                ],
            ],
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
