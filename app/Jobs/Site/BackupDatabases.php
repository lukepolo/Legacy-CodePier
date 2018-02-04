<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Jobs\Server\BackupDatabase;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BackupDatabases implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->site->provisionedServers as $server) {
            dispatch(
                (new BackupDatabase($server))
                    ->onQueue(config('queue.channels.backups'))
            );
        }
    }
}
