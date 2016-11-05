<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\Site;
use Illuminate\Http\Request;

class SiteServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siteId)
    {
        return response()->json(
            Site::where('id', $siteId)->firstorFail()->servers
        );
    }

    /**
     * Stores a site.
     *
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::where('id', $siteId)->firstorFail();

        $site->servers()->sync($request->get('connected_servers', []));

        $site->fire('saved');

        return response()->json($site);
    }
}
