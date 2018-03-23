<?php

namespace App\Http\Controllers\Server;

use App\Models\Backup;
use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Jobs\Server\CreateServer;
use App\Jobs\RestoreDatabaseBackup;
use App\Http\Controllers\Controller;
use App\Models\Server\ProvisioningKey;
use App\Jobs\Server\UpdateSudoPassword;
use App\Services\Systems\SystemService;
use App\Http\Requests\Server\ServerRequest;
use App\Models\Server\Provider\ServerProvider;
use App\Services\Server\Providers\CustomProvider;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class ServerController extends Controller
{
    private $siteService;
    private $serverService;
    private $remoteTaskService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param \App\Services\Site\SiteService| SiteService $siteService
     */
    public function __construct(ServerService $serverService, RemoteTaskService $remoteTaskService, SiteService $siteService)
    {
        $this->siteService = $siteService;
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;

        $this->middleware('checkMaxServers')
            ->except([
                'index',
                'show',
                'destroy',
            ]);

        $this->middleware('checkServerCreationLimit')
            ->only([
                'store',
            ]);
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
            $request->has('trashed') ? Server::onlyTrashed()->get() : Server::get()
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
        }

        $server = Server::create([
            'user_id' => \Auth::user()->id,
            'name' => $request->get('server_name'),
            'server_provider_id' => $request->get('server_provider_id', ServerProvider::where('provider_class', CustomProvider::class)->first()->id),
            'user_server_provider_id' => $request->get('user_server_provider_id', null),
            'status' => $request->has('custom') ? '' : 'Queued For Creation',
            'progress' => '0',
            'options' => $request->only([
                'server_region',
                'server_option',
            ]),
            'port' =>  $request->get('port', 22),
            'server_provider_features' => $request->get('server_provider_features'),
            'server_features' => $request->get('services'),
            // TODO - currently we only support ubuntu 16.04
            'system_class' => 'ubuntu 16.04',
            'type' => $request->user()->subscribed() ? $request->get('type', SystemService::FULL_STACK_SERVER) : SystemService::FULL_STACK_SERVER,
        ]);

        if (! empty($site)) {
            $site->servers()->save($server);
            $this->siteService->resetWorkflow($site);
        }

        if ($request->has('custom')) {
            ProvisioningKey::generate(\Auth::user(), $server);

            $server->refresh();

            $server->update([
                'custom_server_url' => $this->getCustomServerScriptUrl(
                    ProvisioningKey::generate(\Auth::user(), $server)
                ),
            ]);
        } else {
            dispatch(
                (new CreateServer(ServerProvider::findorFail($request->get('server_provider_id')), $server))
                    ->onQueue(config('queue.channels.server_provisioning'))
            );
        }

        return response()->json($server->load(['serverProvider']));
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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

    /**
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSudoPassword($serverId)
    {
        $server = Server::findOrFail($serverId);

        return response()->json($server->sudo_password);
    }

    /**
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatabasePassword($serverId)
    {
        $server = Server::findOrFail($serverId);

        return response()->json($server->database_password);
    }

    /**
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshSudoPassword($serverId)
    {
        /** @var Server $server */
        $server = Server::findOrFail($serverId);

        $server->generateSudoPassword();

        dispatch(new UpdateSudoPassword($server, $server->sudo_password));

        return response()->json($server->sudo_password);
    }
    
    /**
     * @param Server $server
     * @param Backup $backup
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function restoreDatabaseBackup(Server $server, Backup $backup)
    {
        $user = $server->user;
        
        if ($user->subscribed()) {
            dispatch((
                new RestoreDatabaseBackup($server, $backup)
            )->onQueue(
                config('queue.channels.server_commands')
            ));

            return response()->json('OK');
        }

        return response()->json('You must be subscribed to use backups', 401);
    }
}
