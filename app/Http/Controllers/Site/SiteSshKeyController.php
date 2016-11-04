<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\SiteSshKey;
use Illuminate\Http\Request;

/**
 * Class SiteSshKeyController.
 */
class SiteSshKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(SiteSshKey::where('site_id', $siteId)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        return response()->json(
            SiteSshKey::create([
                'site_id' => $siteId,
                'name'      => $request->get('name'),
                'ssh_key'   => trim($request->get('ssh_key')),
            ])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(SiteSshKey::where('site_id', $siteId)->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $siteId, $id)
    {
        $siteSshKey = SiteSshKey::where('site_id', $siteId)->findOrFail($id);

        return response()->json(
            $siteSshKey->update([
                'name'      => $request->get('name'),
                'ssh_key'   => trim($request->get('ssh_key')),
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        return response()->json(SiteSshKey::where('site_id', $siteId)->findOrFail($id)->delete());
    }
}
