<?php

namespace App\Jobs;

use App\Jobs\Server\RunBitt;
use App\Models\Bitt;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class GlobalBitt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $bitt;
    private $silent;

    /**
     * Create a new job instance.
     *
     * @param Bitt $bitt
     * @param bool $silent
     */
    public function __construct(Bitt $bitt, $silent = false)
    {
        $this->bitt = $bitt;
        // TODO - it would be nice to do this
        $this->silent = $silent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('servers')
            ->where('deleted_at', '=', null)
            ->orderBy('id')
            ->chunk(100, function ($servers) {
                foreach ($servers as $server) {
                    $serverModel = new Server((array) $server);
                    $serverModel->id = $server->id;

                    dispatch(
                        (new RunBitt($serverModel, $this->bitt))
                            ->onQueue(config('queue.channels.server_commands'))
                    );
                }
            });
    }
}
