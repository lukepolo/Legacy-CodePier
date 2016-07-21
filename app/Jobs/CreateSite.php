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

    protected $site;
    protected $server;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param string $domain
     * @param bool $wildcardDomain
     * @param null $webDirectory
     */
    public function __construct(Server $server, $domain = 'default', $wildcardDomain = false, $webDirectory = null)
    {
        $this->server = $server;

        $this->site = Site::create([
            'domain' => $domain,
            'server_id' => $server->id,
            'user_id' => $server->user_id,
            'zerotime_deployment' => true,
            'web_directory' => $webDirectory,
            'wildcard_domain' => $wildcardDomain,
        ]);
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        $siteService->create($this->server, $this->site);
    }
}
