<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\BackupDatabase;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BackupDatabases implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelCommandTrait;

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
        $siteCommand = $this->makeCommand($this->site, $this->site, 'Backing Up Databases');

        foreach ($this->site->provisionedServers as $server) {
            $databases = [];

            foreach ($this->site->schemas as $schema) {
                $databases[] = $schema;
            }

            dispatch(
                (new BackupDatabase($server, $databases, $siteCommand))
                    ->onQueue(config('queue.channels.backups'))
            );
        }
    }
}
