<?php

namespace App\Http\Controllers\Site;

use App\Events\Sites\SiteSshKeyCreated;
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

        event(new SiteSshKeyCreated($site, $sshKey));

        return response()->json($sshKey);
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
        $site = Site::findOrFail($siteId);

        event(new SiteSshKeyCreated($site, $site->sshKeys->get($id)));

        return response()->json('OK');
    }
}
