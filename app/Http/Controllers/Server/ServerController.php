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
        return response()->json($request->has('trashed') ? Server::onlyTrashed()->get() : Server::with('serverProvider')->where('pile_id', \Request::get('pile_id'))->get());
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
            'options' => \Request::except(['_token', 'service', 'server_features']),
            'features' => \Request::get('server_features'),
            'pile_id' => \Request::get('pile_id'),
            'system_class' => 'ubuntu 16.04',
        ]);

        $this->dispatch(new CreateServer(
            ServerProvider::findorFail(\Request::get('server_provider_id')),
            $server
        ));
//            ->onQueue('server_creations'));

        return response()->json($server);

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

        // TODO - should be a plugin
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFile(Request $request, $serverID)
    {
        return response()->json($this->serverService->getFile(Server::findOrFail($serverID), $request->get('path')));
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
