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
        return response()->json($request->has('trashed') ? Server::onlyTrashed()->get() : Server::get());
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

    /**
     * Restarts the databases on a server
     *
     * @param Request $request
     */
    public function restartDatabases(Request $request)
    {
        $this->serverService->restartDatabase(Server::findOrFail($request->get('server_id')));
    }

    /**
     * Installs blackfire on a server
     *
     * @param Request $request
     */
    public function installBlackfire(Request $request)
    {
        $this->serverService->installBlackFire(
            Server::findOrFail($request->get('server_id')),
            \Request::get('server_id'),
            \Request::get('server_token'));
    }

    /**
     * Gets the disk space for a server
     *
     * @param Request $request
     * @return \App\Classes\DiskSpace
     */
    public function getDiskSpace(Request $request)
    {
        return $this->serverService->checkDiskSpace($request->get('server_id'));
    }

    /**
     * Saves a file to a server
     * @param Request $request
     */
    public function saveFile(Request $request)
    {
        $this->serverService->saveFile(Server::findOrFail($request->get('server_id')), $request->get('path'),
            $request->get('file'));
    }

    /**
     * Gets all server regions and options for a server provider
     *
     * @param Request $request
     */
    public function getServerOptionsAndRegions(Request $request)
    {
        $serverProvider = ServerProvider::findOrFail($request->get('serverProviderID'));
        $this->serverService->getServerOptions($serverProvider);
        $this->serverService->getServerRegions($serverProvider);
    }

    /**
     * Restarts a server
     *
     * @param Request $request
     */
    public function restartServer(Request $request)
    {
        $this->serverService->restartServer(Server::findOrFail($request->get('server_id')));
    }

    /**
     * Restart the servers web services
     *
     * @param Request $request
     */
    public function restartWebServices(Request $request)
    {
        $this->serverService->restartWebServices(Server::findOrFail($request->get('server_id')));
    }

    /**
     * Restarts the worker services
     *
     * @param Request $request
     */
    public function restartWorkerServices(Request $request)
    {
        $this->serverService->restartWorkers(Server::findOrFail($request->get('server_id')));
    }
}
