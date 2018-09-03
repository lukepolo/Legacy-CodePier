<?php

namespace App\Console\Commands\Tests\Notifications;

use App\Models\Server\Server;
use Illuminate\Console\Command;
use App\Notifications\Server\ServerDiskUsage;

class DiskMonitorNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:disk-notification {serverId}';

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

        $diskName = '/dev/vda1';

        $server->update([
            'stats->disk_usage->'.$diskName => [
                "disk" => "/dev/vda1",
                "used" => "9216",
                "percent" => "99%",
                "available"=> "1024"
            ]
        ]);

        $server->notify(new ServerDiskUsage($server));
    }
}
