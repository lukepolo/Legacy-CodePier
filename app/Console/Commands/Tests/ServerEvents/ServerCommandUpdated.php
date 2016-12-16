<?php

namespace App\Console\Commands\Tests\ServerEvents;

use App\Models\ServerCommand;
use Illuminate\Console\Command;

class ServerCommandUpdated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:server-commands {serverCommandId}';

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
        $serverCommand = ServerCommand::findOrFail($this->argument('serverCommandId'));

        $serverCommand->update([
            'started' => ! $serverCommand->started,
            'completed' => $serverCommand->started,
        ]);
    }
}
