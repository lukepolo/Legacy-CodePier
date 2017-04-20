<?php

namespace App\Http\Controllers;

use App\Jobs\Server\SshKeys\InstallServerEnvironmentVariable;
use App\Jobs\Server\SshKeys\RemoveServerEnvironmentVariable;
use App\Models\EnvironmentVariable;
use App\Models\Server\Server;
use Illuminate\Http\Request;

class ServerEnvironmentVariablesController extends Controller
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
            Server::with('environmentVariables')->findOrFail($serverId)->environmentVariables
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        $server = Server::with('environmentVariables')->findOrFail($serverId);

        $variable = $request->get('variable');

        if (! $server->environmentVariables
            ->where('variable', $variable)
            ->count()
        ) {
            $environmentVariable = EnvironmentVariable::create([
                'variable' => $variable,
                'value' => $request->get('value')
            ]);

            $server->environmentVariables()->save($environmentVariable);

            $this->dispatch(
                (new InstallServerEnvironmentVariable($server, $environmentVariable))->onQueue(config('queue.channels.server_commands'))
            );

            return response()->json($environmentVariable);
        }

        return response()->json('SSH Key Already Exists', 400);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $serverId)
    {
        $server = Server::with('environmentVariables')->findOrFail($serverId);

        $this->dispatch(
            (new RemoveServerEnvironmentVariable($server, $server->environmentVariables->keyBy('id')->get($id)))->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json($server->environmentVariables()->detach($id));
    }
}
