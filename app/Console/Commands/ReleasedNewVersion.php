<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReleasedNewVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the new version to pusher, to notify users their has been an update';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        event(new \App\Events\ReleasedNewVersion());
    }
}
