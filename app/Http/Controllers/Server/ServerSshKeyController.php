<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\ServerSshKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\Server\ServerSshKeyRequest;

class ServerSshKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            ServerSshKey::where('server_id', $serverId)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServerSshKeyRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(ServerSshKeyRequest $request, $serverId)
    {
        return response()->json(
            ServerSshKey::create([
                'server_id' => $serverId,
                'name'      => $request->get('name'),
                'ssh_key'   => trim($request->get('ssh_key')),
            ])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($serverId, $id)
    {
        return response()->json(
            ServerSshKey::where('server_id', $serverId)->findOrFail($id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        return response()->json(
            ServerSshKey::where('server_id', $serverId)->findOrFail($id)->delete()
        );
    }
}
