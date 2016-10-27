<?php

namespace App\Http\Controllers\Site\Certificate;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site\Site;
use App\Models\Site\SiteSslCertificate;
use Illuminate\Http\Request;

/**
 * Class SiteSSLController.
 */
class SiteSSLController extends Controller
{
    private $siteService;

    /**
     * SiteSSLController constructor.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

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
        return response()->json(SiteSslCertificate::where('site_id', $siteId)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(SiteSslCertificate::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        SiteSslCertificate::findOrFail($id)->delete();

        return;
        $this->siteService->removeSSL();
    }

    /**
     * Activates a ssl certificate.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function activateSSL(Request $request)
    {
        $errors = $this->siteService->activateSSL(SiteSslCertificate::findOrFail($request->get('site_ssl_certificate_id')));

        if (is_array($errors)) {
            return back()->withErrors($errors);
        }
    }

    /**
     * Deactivates a ssl certificate.
     *
     * @param Request $request
     * @param $siteId
     */
    public function deactivateSSL(Request $request, $siteId)
    {
        $this->siteService->deactivateSSL(Site::findOrFail($siteId));
    }
}
