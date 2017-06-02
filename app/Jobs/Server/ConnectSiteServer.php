<?php

namespace App\Jobs\Server;


use Carbon\Carbon;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;

class ConnectSiteServer implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $site;
    protected $server;
    protected $provision;

    public $tries = 1;
    public $timeout = 10;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     * @param \App\Models\Server\Server $server
     */
    public function __construct(Site $site, Server $server)
    {
        $this->site = $site;
        $this->server = $server;
    }

    /**
     * Execute the job.
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        if ($this->server->ip) {
           $siteService->createFirewallRule(
               $this->site,
               '*',
               'both',
               'Opens your '.$this->server->type .' to your site',
               $this->server->ip
           );
        } else {

            if ($this->server->created_at->addMinutes(5) > Carbon::now()) {

                dispatch(
                    (new self($this->site, $this->server))->delay(30)->onQueue(config('queue.channels.server_commands'))
                );

                return;
            }

        }
    }
}
