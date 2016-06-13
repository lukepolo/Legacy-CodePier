<?php

namespace App\Jobs;

use App\Contracts\Server\Site\SiteServiceContract;
use App\Models\Server;
use App\Services\Server\Site\SiteService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateSite extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $domain;
    protected $server;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param $domain
     */
    public function __construct(Server $server, $domain = null)
    {
        $this->server = $server;
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\Site\SiteService | SiteServiceContract $siteService
     */
    public function handle(SiteService $siteService)
    {
        $siteService->create($this->server, $this->domain);
    }
}
