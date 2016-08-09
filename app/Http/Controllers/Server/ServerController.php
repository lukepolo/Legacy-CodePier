<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Jobs\CreateServer;
use App\Models\Server;
use App\Models\ServerProvider;
use Illuminate\Http\Request;

/**
 * Class ServerController
 *
 * @package App\Http\Controllers\Server
 */
class ServerController extends Controller
{
    private $serverService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json($request->has('trashed') ? Server::onlyTrashed()->get() : Server::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $server = Server::create([
            'user_id' => \Auth::user()->id,
            'name' => \Request::get('name'),
            'server_provider_id' => (int)\Request::get('server_provider_id'),
            'status' => 'Queued For Creation',
            'progress' => '0',
            'options' => \Request::except(['_token', 'service', 'features']),
            'features' => array_keys(\Request::get('features'))
        ]);


        $this->dispatch(new CreateServer(
            ServerProvider::findorFail(\Request::get('server_provider_id')),
            $server
        ));
//            ->onQueue('server_creations'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Server::with('sites')->findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Server::findOrFail($id)->delete();
    }

    /**
     * Restores a server
     *
     * @param $id
     */
    public function restore($id)
    {
        Server::onlyTrashed()->findOrFail($id)->restore();
    }
}
