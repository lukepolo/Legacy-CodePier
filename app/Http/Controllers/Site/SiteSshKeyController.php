<?php

namespace App\Http\Controllers\Site;

use App\Models\SshKey;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteSshKeyRequest;

class SiteSshKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->sshKeys
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteSshKeyRequest $request
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(SiteSshKeyRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $sshKey = SshKey::create([
            'site_id' => $siteId,
            'name' => $request->get('name'),
            'ssh_key' => trim($request->get('ssh_key')),
        ]);

        $site->sshKeys()->save($sshKey);

        return response()->json($sshKey);
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
        return response()->json(
            Site::findOrFail($siteId)->get($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SiteSshKeyRequest $request
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SiteSshKeyRequest $request, $siteId, $id)
    {
        $siteSshKey = Site::findOrFail($siteId)->get($id);

        return response()->json(
            $siteSshKey->update([
                'name' => $request->get('name'),
                'ssh_key' => trim($request->get('ssh_key')),
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
        return response()->json(
            Site::findOrFail($siteId)->get($id)->delete()
        );
    }
}
