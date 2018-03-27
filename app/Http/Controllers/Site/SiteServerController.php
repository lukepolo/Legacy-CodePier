<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Jobs\Site\CreateSite;
use App\Jobs\Site\DeleteSite;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteServerRequest;
use App\Contracts\Site\SiteServiceContract as SiteService;

class SiteServerController extends Controller
{
    private $siteService;

    /**
     * SiteServerController constructor.
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

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

        if ($site->domain === 'default') {
            foreach ($request->get('connected_servers', []) as $serverId) {
                $server = Server::with(['sites' => function ($query) use ($site) {
                    return $query->where('site_id', '!=', $site->id);
                }])->findOrFail($serverId);
                $defaultSite = $server->sites->first(function ($site) {
                    return $site->domain === 'default';
                });

                if (! empty($defaultSite)) {
                    return response()->json('You cannot attach another domainless site to this server', 400);
                }
            }
        }

        $changes = $site->servers()->sync($request->get('connected_servers', []));

        foreach ($changes['attached'] as $attached) {
            dispatch(
                (new CreateSite(Server::findOrFail($attached), $site))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        }

        foreach ($changes['detached'] as $detached) {
            dispatch(
                (new DeleteSite(Server::findOrFail($detached), $site))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        }

        $this->siteService->resetWorkflow($site);

        return response()->json($site);
    }
}
