<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Jobs\Site\CreateSite;
use App\Jobs\Site\DeleteSite;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteServerRequest;

class SiteServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::where('id', $siteId)->firstorFail()->servers
        );
    }

    /**
     * Stores a site.
     *
     * @param SiteServerRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteServerRequest $request, $siteId)
    {
        $site = Site::where('id', $siteId)->firstorFail();

        $changes = $site->servers()->sync($request->get('connected_servers', []));

        foreach ($changes['attached'] as $attached) {
            $this->dispatch(
                (new CreateSite(Server::findOrFail($attached), $site))->onQueue(config('queue.channels.server_commands'))
            );
        }

        foreach ($changes['detached'] as $detached) {
            $this->dispatch(
                (new DeleteSite(Server::findOrFail($detached), $site))->onQueue(config('queue.channels.server_commands'))
            );
        }

        // This reset their workflow as they may need to update these areas of their sites configuration
        if ($site->servers->count() > 1) {
            $site->update([
                'workflow' => [
                    'site_deployment' => [
                        'step' => 'site_deployment',
                        'order' => 1,
                        'completed' => false,
                    ],
                    'site_cron_jobs' => [
                        'step' => 'site_cron_jobs',
                        'order' => 2,
                        'completed' => false,
                    ],
                    'site_daemons' => [
                        'step' => 'site_daemons',
                        'order' => 3,
                        'completed' => false,
                    ],
                ],
            ]);
        }

        return response()->json($site);
    }
}
