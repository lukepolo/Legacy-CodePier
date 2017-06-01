<?php

namespace App\Http\Controllers\Server;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Jobs\Server\CreateServer;
use App\Http\Controllers\Controller;
use App\Models\Server\ProvisioningKey;
use App\Services\Systems\SystemService;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Server\ServerRequest;
use App\Models\Server\Provider\ServerProvider;
use App\Services\Server\Providers\CustomProvider;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class ServerController extends Controller
{
    private $serverService;
    private $remoteTaskService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(ServerService $serverService, RemoteTaskService $remoteTaskService)
    {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
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
            $request->has('trashed') ?
                Server::onlyTrashed()->get() :
                Server::when($request->has('pile_id'), function (Builder $query) use ($request) {
                    return $query->where('pile_id', $request->get('pile_id'));
                })
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param ServerRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServerRequest $request)
    {
        $site = null;
        if ($request->has('site')) {
            $site = Site::findOrFail($request->get('site'));

            $pileId = $site->pile_id;
        } else {
            $pileId = $request->get('pile_id');
        }

        $server = Server::create([
            'user_id' => \Auth::user()->id,
            'name' => $request->get('server_name'),
            'server_provider_id' => $request->get('server_provider_id', ServerProvider::where('provider_class', CustomProvider::class)->first()->id),
            'status' => $request->has('custom') ? '' : 'Queued For Creation',
            'progress' => '0',
            'options' => $request->only([
                'server_region',
                'server_option',
            ]),
            'port' =>  $request->get('port', 22),
            'server_provider_features' => $request->get('server_provider_features'),
            'server_features' => $request->get('services'),
            'pile_id' => $pileId,
            // TODO - currently we only support ubuntu 16.04
            'system_class' => 'ubuntu 16.04',
            'type' => $request->get('type', SystemService::FULL_STACK_SERVER),
        ]);

        if (! empty($site)) {
            $site->servers()->save($server);
        }

        if ($request->has('custom')) {
            $key = ProvisioningKey::generate(\Auth::user(), $server);

            $server->update([
                'custom_server_url' => $this->getCustomServerScriptUrl(
                    ProvisioningKey::generate(\Auth::user(), $server)
                ),
            ]);
        } else {
            $this->dispatch((new CreateServer(
                ServerProvider::findorFail($request->get('server_provider_id')),
                $server
            ))->onQueue(config('queue.channels.server_provisioning')));
        }

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
        ])->restore();

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
        return response()->json(Server::findOrFail($id));
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
        return response()->json(Server::findOrFail($id)->delete());
    }

    /**
     * Restores a server.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $server = Server::onlyTrashed()->findOrFail($id);

        $server->restore();

        return response()->json($server);
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

    /**
     * @param ProvisioningKey $key
     * @return string
     */
    public function getCustomServerScriptUrl(ProvisioningKey $key)
    {
        return 'curl '.config('app.url_provision').' | bash -s '.$key->key;
    }
}
