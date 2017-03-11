<?php

namespace App\Http\Controllers\Server;

use App\Models\Schema;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchemaRequest;
use App\Jobs\Server\Schemas\AddServerSchema;
use App\Jobs\Server\Schemas\RemoveServerSchema;

class ServerSchemaController extends Controller
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
            Server::findOrFail($serverId)->schemas
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SchemaRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(SchemaRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $name = $request->get('name');
        $database = $request->get('database');

        if (! $server->schemas
            ->where('name', $name)
            ->where('database', $database)
            ->count()
        ) {
            $schema = Schema::create([
                'name' => $name,
                'database' =>  $database,
            ]);

            $this->dispatch(
                (new AddServerSchema($server, $schema))->onQueue(config('queue.channels.server_commands'))
            );

            return response()->json($schema);
        }

        return response()->json('Schema already Exists', 400);
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

        $this->dispatch(
            (new RemoveServerSchema($server, $server->schemas->keyBy('id')->get($id)))->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json('OK');
    }
}
