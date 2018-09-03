<?php

namespace App\Console\Commands\Tests\Notifications;

use App\Models\Server\Server;
use Illuminate\Console\Command;
use App\Notifications\Server\ServerMemory;

class MemoryMonitorNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:memory-notification {serverId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = Server::findOrFail($this->argument('serverId'));

        $memoryStats = [
            'Mem' => [
                "free" => "128",
                "name" => "Swap",
                "used" => "500",
                "total" => "2048",
                "available" => "250"
            ],
            'Swap' => [
                "free" => "75",
                "name" => "Swap",
                "used" => "1024",
                "total" => "2048",
                "available" => "512"
            ]
        ];

        foreach ($memoryStats as $name => $stats) {
            $server->update([
            'stats->memory->'.$name => $stats
        ]);
        }

        $server->notify(new ServerMemory($server));
    }
}
