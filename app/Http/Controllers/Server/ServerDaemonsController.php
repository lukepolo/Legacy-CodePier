<?php

namespace App\Http\Controllers\Server;

use App\Models\Daemon;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\DaemonRequest;
use App\Http\Requests\DaemonUpdatedRequest;
use App\Jobs\Server\Daemons\RemoveServerDaemon;
use App\Jobs\Server\Daemons\InstallServerDaemon;

class ServerDaemonsController extends Controller
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
            Server::findOrFail($serverId)->daemons
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DaemonRequest $request
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DaemonRequest $request, $serverId)
    {
        /** @var Server $server */
        $server = Server::findOrFail($serverId);

        $daemon = Daemon::create([
            'user' => $request->get('user'),
            'command' => $request->get('command'),
            'server_ids' => $request->get('servers', []),
            'server_types' => $request->get('server_types', []),
        ]);

        $server->daemons()->save($daemon);

        dispatch(
            (new InstallServerDaemon($server, $daemon))
                ->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json($daemon);
    }

    /**
     * @param $request
     * @param $serverId
     * @param $cronJobId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DaemonUpdatedRequest $request, $serverId, $cronJobId)
    {
        /** @var Server $server */
        $server = Server::with('cronJobs')->findOrFail($serverId);

        $daemon = Daemon::findOrFail($cronJobId);

        $daemon->update([
            'server_ids' => $request->get('servers', []),
            'server_types' => $request->get('server_types', []),
        ]);

        return response()->json($daemon);
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
        $server = Server::with('daemons')->findOrFail($serverId);

        event(new RemoveServerDaemon($server, $server->daemons->keyBy('id')->get($id)));

        return response()->json('OK');
    }
}
