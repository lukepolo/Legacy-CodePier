<?php

namespace App\Http\Controllers\Site;

use App\Models\SshKey;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\SshKeyRequest;
use App\Events\Sites\SiteSshKeyCreated;
use App\Events\Sites\SiteSshKeyDeleted;

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
     * @param SshKeyRequest $request
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(SshKeyRequest $request, $siteId)
    {
        $site = Site::with('sshKeys')->findOrFail($siteId);
        $sshKey = trim($request->get('ssh_key'));

        if(!$site->sshKeys
            ->where('ssh_key', $sshKey)
            ->count()
        ) {
            $sshKey = SshKey::create([
                'name' => $request->get('name'),
                'ssh_key' => $sshKey,
            ]);

            $site->sshKeys()->save($sshKey);

            event(new SiteSshKeyCreated($site, $sshKey));

            return response()->json($sshKey);
        }

        return response()->json('SSH Key Already Exists', 400);
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
        $site = Site::with('sshKeys')->findOrFail($siteId);

        event(new SiteSshKeyDeleted($site, $site->sshKeys->keyBy('id')->get($id)));

        return response()->json($site->sshKeys()->detach($id));
    }
}
