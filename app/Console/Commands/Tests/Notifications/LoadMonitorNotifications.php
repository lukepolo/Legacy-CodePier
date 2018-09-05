<?php

namespace App\Console\Commands\Tests\Notifications;

use App\Models\Server\Server;
use Illuminate\Console\Command;
use App\Notifications\Server\ServerLoad;

class LoadMonitorNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:load-notification {serverId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test load notifications';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = Server::findOrFail($this->argument('serverId'));
        $server->update([
            'stats->cpus' => 1,
            'stats->loads' => [1.2, 1, .5],
        ]);

        $server->notify(new ServerLoad($server));
    }
}
