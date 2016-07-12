<?php

namespace App\Jobs;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Models\Server;
use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreateSite
 * @package App\Jobs
 */
class CreateSite extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $domain;
    protected $server;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param $domain
     */
    public function __construct(Server $server, $domain = 'default')
    {
        $this->server = $server;
        $this->domain = $domain;

        Site::create([
            'domain' => $domain,
            'server_id' => $server->id,
            'wildcard_domain' => false,
            'zerotime_deployment' => false,
            'user_id' => $server->user_id,
            'path' => '/home/codepier/' . $domain
        ]);
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        $siteService->create($this->server, $this->domain);
    }
}
