<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Models\EnvironmentVariable;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnvironmentVariableRequest;
use App\Jobs\Server\EnvironmentVariables\RemoveServerEnvironmentVariable;
use App\Jobs\Server\EnvironmentVariables\InstallServerEnvironmentVariable;

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
     * @param EnvironmentVariableRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(EnvironmentVariableRequest $request, $serverId)
    {
        $server = Server::with('environmentVariables')->findOrFail($serverId);

        $variable = $request->get('variable');

        if (! $server->environmentVariables
            ->where('variable', $variable)
            ->count()
        ) {
            $environmentVariable = EnvironmentVariable::create([
                'variable' => $variable,
                'value' => $request->get('value'),
            ]);

            $this->dispatch(
                (new InstallServerEnvironmentVariable($server, $environmentVariable))->onQueue(config('queue.channels.server_commands'))
            );

            return response()->json($environmentVariable);
        }

        return response()->json('Environment Variable Already Exists', 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        $server = Server::with('environmentVariables')->findOrFail($serverId);

        $this->dispatch(
            (new RemoveServerEnvironmentVariable($server, $server->environmentVariables->keyBy('id')->get($id)))->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json($server->environmentVariables()->detach($id));
    }
}
