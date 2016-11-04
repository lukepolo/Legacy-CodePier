<?php

namespace App\Http\Controllers\Site\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Site\SiteSslCertificate;
use Illuminate\Http\Request;

/**
 * Class SiteSSLController.
 */
class SiteSSLController extends Controller
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
            SiteSslCertificate::where('site_id', $siteId)->get()
        );
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
        return response()->json(
            SiteSslCertificate::where('site_id', $siteId)->findOrFail($id)
        );
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
        return response()->json(
            SiteSslCertificate::where('site_id', $siteId)->findOrFail($id)->delete()
        );
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
        dd('NEW METHOD');
    }

    /**
     * Deactivates a ssl certificate.
     *
     * @param Request $request
     * @param $siteId
     */
    public function deactivateSSL(Request $request, $siteId)
    {
        dd('NEW METHOD');
    }
}
