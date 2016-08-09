<?php

namespace App\Http\Controllers\Server\Features;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerProvider;
use Illuminate\Http\Request;

/**
 * Class ServerCommandsController
 *
 * @package App\Http\Controllers\Server\Features
 */
class ServerCommandsController extends Controller
{
    /**
     * @var ServerService|\App\Services\Server\ServerService
     */
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
     * Tests a ssh connection to server
     *
     * @param Request $request
     */
    public function testSSHConnection(Request $request)
    {
        $this->serverService->testSSHConnection(Server::findOrFail($request->get('server_id')));
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
