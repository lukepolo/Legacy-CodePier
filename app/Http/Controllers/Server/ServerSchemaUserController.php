<?php

namespace App\Http\Controllers\Server;

use App\Models\SchemaUser;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchemaUserRequest;
use App\Jobs\Server\Schemas\AddServerSchemaUser;
use App\Jobs\Server\Schemas\RemoveServerSchemaUser;

class ServerSchemaUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->schemaUsers
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SchemaUserRequest $request
     * @param int               $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SchemaUserRequest $request, $serverId)
    {
        $server = Server::with('schemaUsers')->findOrFail($serverId);

        $name = $request->get('name');

        if (! $server->schemaUsers
            ->where('name', $name)
            ->count()
        ) {
            $schemaUser = SchemaUser::create([
                'name' => $name,
                'password' => $request->get('password'),
                'schema_ids' =>  $request->get('schema_ids'),
            ]);

            dispatch(
                (new AddServerSchemaUser($server, $schemaUser))
                    ->onQueue(config('queue.channels.server_commands'))
            );

            return response()->json($schemaUser);
        }

        return response()->json('Schema user already Exists', 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        $server = Server::findOrFail($serverId);

        dispatch(
            (new RemoveServerSchemaUser($server, $server->schemaUsers->keyBy('id')->get($id)))
                ->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json('OK');
    }
}
