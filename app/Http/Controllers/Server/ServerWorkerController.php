<?php

namespace App\Http\Controllers\Server;

use App\Models\Worker;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Jobs\Server\Workers\InstallServerWorker;
use App\Http\Requests\Server\ServerWorkerRequest;

class ServerWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->workers
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @param ServerWorkerRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(ServerWorkerRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $worker = Worker::create([
            'user'              => $request->get('user'),
            'command'           => $request->get('command'),
            'auto_start'        => $request->get('auto_start', 0),
            'auto_restart'      => $request->get('auto_restart', 0),
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        $server->workers()->save($worker);

        dispatch(
            (new InstallServerWorker($server, $worker))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json($worker);
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
        $server = Server::findOrFail($serverId);

        dispatch(
            (new InstallServerWorker($server, $server->workers->get($id)))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json('OK');
    }
}
