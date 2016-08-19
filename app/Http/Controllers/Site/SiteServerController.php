<?php

namespace App\Http\Controllers\Site;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Class SiteServerController
 * @package App\Http\Controllers\Site
 */
class SiteServerController extends Controller
{
    private $siteService;

    /**
     * SiteController constructor.
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(Site::where('id', $request->get('site'))->firstorFail()->servers);
    }

}