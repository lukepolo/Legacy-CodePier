<?php

namespace App\Http\Controllers\Site;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Server\Server;
use App\Models\Server\ServerWorker;
use App\Models\Site\Site;
use App\Models\Site\SiteWorker;
use App\Services\Systems\SystemService;
use Illuminate\Http\Request;

/**
 * Class SiteWorkerController.
 */
class SiteWorkerController extends Controller
{
    private $siteService;
    private $serverService;

    /**
     * RepositoryHookController constructor.
     *
     * @param \App\Services\Site\SiteService |SiteService $siteService
     * @param ServerService $serverService
     */
    public function __construct(SiteService $siteService, ServerService $serverService)
    {
        $this->serverService = $serverService;
        $this->siteService = $siteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siteId)
    {
        return response()->json(SiteWorker::where('site_id', $siteId)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $siteWorker = SiteWorker::create([
            'site_id' => $site->id,
            'command' => $request->get('command'),
            'auto_start' => true,
            'auto_restart' => true,
            'user' => 'codepier',
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        foreach ($request->get('selected_servers') as $serverId) {
            $serverWorker = ServerWorker::create([
                'server_id' => $serverId,
                'command' => $siteWorker->command,
                'auto_start' => $siteWorker->auto_start,
                'auto_restart' => $siteWorker->auto_restart,
                'number_of_workers' => $siteWorker->number_of_workers,
                'site_worker_id' => $siteWorker->id,
                'user' => $siteWorker->user,
            ]);

            $server = Server::findOrFail($serverId);

            $this->runOnServer($server, function () use ($server, $serverWorker) {
                $this->serverService->getService(SystemService::WORKERS, $server)->addWorker($serverWorker);
            });
        }

        return $this->remoteResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(SiteWorker::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        $site = Site::findOrFail($siteId);
        $siteWorker = SiteWorker::with('serverWorkers.server')->findOrFail($id);

        foreach ($siteWorker->serverWorkers as $serverWorker) {
            $server = $serverWorker->server;

            $this->runOnServer($server, function () use ($server, $serverWorker) {
                $this->serverService->getService(SystemService::WORKERS, $server)->removeWorker($serverWorker);
                $serverWorker->delete();
            });
        }

        if ($this->successful()) {
            $siteWorker->delete();
        }

        return $this->remoteResponse();
    }
}
