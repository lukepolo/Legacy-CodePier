<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Jobs\Site\CreateSite;
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

        return response()->json($site);
    }
}
