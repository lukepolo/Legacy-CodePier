<?php

namespace App\Console\Commands;

use App\Models\Server\Server;
use Illuminate\Console\Command;
use App\Services\Systems\Ubuntu\V_16_04\MonitoringService;

class TestMonitorScripts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:monitor {serverId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the monitor scripts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = Server::findOrFail($this->argument('serverId'));

        dump(shell_exec(
            MonitoringService::LOAD_AVG_SCRIPT.'
            echo "'.action('WebHookController@loadMonitor', $server->encode()).'?loads=$current_load&cpus=$cpus"
        '));

        dump(shell_exec(
            MonitoringService::MEMORY_SCRIPT.' | while read -r current_memory;  do
                echo "'.action('WebHookController@memoryMonitor', $server->encode()).'?memory=$current_memory"
            done
        '));

        dump(shell_exec(
            MonitoringService::DISK_USAGE_SCRIPT.' | while read -r disk_usage;  do
                echo "'.action('WebHookController@diskUsageMonitor', $server->encode()).'?disk_usage=$disk_usage"
            done
        '));
    }
}
