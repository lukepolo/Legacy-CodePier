<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Server\ServerRequest;
use App\Jobs\Server\CreateServer;
use App\Models\Server\Provider\ServerProvider;
use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(
            $request->has('trashed') ? Server::onlyTrashed()->get() : Server::with(['serverProvider', 'pile'])->where('pile_id', $request->get('pile_id'))->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param ServerRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServerRequest $request)
    {
        if ($request->has('site')) {
            $site = Site::findOrFail($request->get('site'));

            $pileId = $site->pile_id;
        } else {
            $pileId = $request->get('pile_id');
        }

        $server = Server::create([
            'user_id' => \Auth::user()->id,
            'name' => $request->get('server_name'),
            'server_provider_id' => (int) $request->get('server_provider_id'),
            'status' => 'Queued For Creation',
            'progress' => '0',
            'options' => $request->except(['_token', 'server_provider_features']),
            'server_provider_features' => $request->get('server_provider_features'),
            'server_features' => $request->get('services'),
            'pile_id' => $pileId,
            'system_class' => 'ubuntu 16.04',
        ]);

        if (isset($site)) {
            $site->servers()->save($server);
        }

        $this->dispatch(new CreateServer(
            ServerProvider::findorFail($request->get('server_provider_id')),
            $server
        ));
//            ->onQueue('server_creations'));

        return response()->json($server->load(['serverProvider', 'pile']));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $server = Server::findOrFail($id)->update([
            'name' => $request->get('name'),
        ]);

        return response()->json($server);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        print_r(Server::with('sites')->findOrFail($id)->server_features);
        die;
        return response()->json(Server::with('sites')->findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Server::findOrFail($id)->delete();
    }

    /**
     * Restores a server.
     *
     * @param $id
     */
    public function restore($id)
    {
        Server::onlyTrashed()->findOrFail($id)->restore();
    }

    /**
     * Gets the disk space for a server.
     *
     * @param Request $request
     *
     * @return \App\Classes\DiskSpace
     */
    public function getDiskSpace(Request $request)
    {
        return $this->serverService->checkDiskSpace($request->get('server_id'));
    }

    /**
     * Saves a file to a server.
     *
     * @param Request $request
     *
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFile(Request $request, $serverId)
    {
        return response()->json($this->serverService->getFile(Server::findOrFail($serverId), $request->get('file')));
    }

    /**
     * Saves a file to a server.
     *
     * @param Request $request
     * @param $serverId
     */
    public function saveFile(Request $request, $serverId)
    {
        $this->serverService->saveFile(Server::findOrFail($serverId), $request->get('file'), $request->get('content'));
    }

    /**
     * Restarts a server.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartServer($serverId)
    {
        $server = Server::findOrFail($serverId);

        $this->runOnServer(function () use ($server) {
            $this->serverService->restartServer($server);
        });

        return $this->remoteResponse();
    }

    /**
     * Restart the servers web services.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartWebServices($serverId)
    {
        $server = Server::findOrFail($serverId);

        $this->runOnServer(function () use ($server) {
            $this->serverService->restartWebServices($server);
        });

        return $this->remoteResponse();
    }

    /**
     * Restarts the databases on a server.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartDatabases($serverId)
    {
        $server = Server::findOrFail($serverId);

        $this->runOnServer(function () use ($server) {
            $this->serverService->restartDatabase($server);
        });

        return $this->remoteResponse();
    }

    /**
     * Restarts the worker services.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartWorkerServices($serverId)
    {
        $server = Server::findOrFail($serverId);

        $this->runOnServer(function () use ($server) {
            $this->serverService->restartWorkers($server);
        });

        return $this->remoteResponse();
    }

    /**
     * Tests a ssh connection to server.
     *
     * @param $serverId
     */
    public function testSSHConnection($serverId)
    {
        $this->serverService->testSSHConnection(Server::findOrFail($serverId));
    }
}
