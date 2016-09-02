<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Models\Server;
use App\Models\ServerProvisionStep;
use App\Services\Systems\SystemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProvisionServer.
 */
class ProvisionServer implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    /**
     * Create a new job instance.
     *
     * @param Server $server
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
            $this->server->load('provisionSteps');
        }

        if ($serverService->provision($this->server)) {
            foreach ($this->server->user->sshKeys as $sshKey) {
                $serverService->installSshKey($this->server, $sshKey->ssh_key);
            }

            event(new ServerProvisionStatusChanged($this->server, 'Provisioned', 100));
        }
    }

    private function createProvisionSteps(Server $server)
    {
        dd('test');
        $serverSteps = collect([
            SystemService::SYSTEM => [
                'updateSystem'     => 'Updating the system',
                'setTimezoneToUTC' => 'Settings Timezone to UTC',
                'setLocaleToUTF8'  => 'Settings Locale to UTF8',
                'addCodePierUser'  => 'Adding CodePier User',
            ],
            SystemService::FIREWALL => [
                'addBasicFirewallRules' => 'Installing Basic Firewall Rules',
            ],
        ]);

        foreach ($serverSteps as $service => $serviceFunctions) {
            foreach ($serviceFunctions as $function => $step) {
                $this->createStep($server, $service, $function, $step);
            }
        }
    }

    private function createStep(Server $server, $service, $function, $step)
    {
        ServerProvisionStep::create([
            'server_id' => $server->id,
            'service'   => $service,
            'function'  => $function,
            'step'      => $step,
        ]);
    }
}
