<?php

namespace App\Http\Controllers;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Models\SiteSslCertificate;
use Illuminate\Http\Request;

/**
 * Class SiteSSLController
 * @package App\Http\Controllers
 */
class SiteSSLController extends Controller
{
    private $siteService;

    /**
     * SiteSSLController constructor.
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(SiteSslCertificate::where('site_id', $request->get('site_id'))->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(SiteSslCertificate::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->siteService->removeSSL(SiteSslCertificate::findOrFail($id));
    }
}
