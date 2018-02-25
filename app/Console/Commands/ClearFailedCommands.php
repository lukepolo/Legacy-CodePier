<?php

namespace App\Console\Commands;

use App\Models\ServerCommand;
use Illuminate\Console\Command;
use App\Models\Site\SiteServerDeployment;

class ClearFailedCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears any locked';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ServerCommand::where('started', 1)
            ->where('completed', 0)
            ->where('failed', 0)
            ->update([
            'failed' => true,
        ]);

        SiteServerDeployment::where('started', 1)
            ->where('completed', 0)
            ->where('failed', 0)
            ->update([
                'failed' => true,
            ]);
    }
}
