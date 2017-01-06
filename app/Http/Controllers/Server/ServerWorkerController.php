<?php

namespace App\Http\Controllers\Server;

use App\Models\Worker;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkerRequest;
use App\Jobs\Server\Workers\RemoveServerWorker;
use App\Jobs\Server\Workers\InstallServerWorker;

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
     * @param WorkerRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(WorkerRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $worker = Worker::create([
            'user' => $request->get('user'),
            'command' => $request->get('command'),
            'auto_start' => $request->get('auto_start', false),
            'auto_restart' => $request->get('auto_restart', false),
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
            (new RemoveServerWorker($server,
                $server->workers->keyBy('id')->get($id)))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json('OK');
    }
}
