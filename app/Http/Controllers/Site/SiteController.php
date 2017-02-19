<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Jobs\Site\CreateSite;
use App\Jobs\Site\DeploySite;
use App\Models\Server\Server;
use App\Models\Site\SiteDeployment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteRequest;
use App\Http\Requests\Site\DeploySiteRequest;
use App\Http\Requests\Site\SiteRepositoryRequest;
use App\Http\Requests\Site\SiteServerFeatureRequest;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;

class SiteController extends Controller
{
    private $serverService;
    private $repositoryService;

    /**
     * SiteController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(ServerService $serverService, RepositoryService $repositoryService)
    {
        $this->serverService = $serverService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            Site::get()
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param SiteRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteRequest $request)
    {
        $site = Site::create([
            'user_id'             => \Auth::user()->id,
            'domain'              => $request->get('domainless') == true ? 'default' : $request->get('domain'),
            'pile_id'             => $request->get('pile_id'),
            'name'                => $request->get('domain'),
        ]);

        return response()->json($site);
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
        return response(
            Site::findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SiteRepositoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRepositoryRequest $request, $id)
    {
        $site = Site::findOrFail($id);

        $site->update([
            'type'                        => $request->get('type'),
            'branch'                      => $request->get('branch'),
            'framework'                   => $request->get('framework'),
            'repository'                  => $request->get('repository'),
            'web_directory'               => $request->get('web_directory'),
            'keep_releases'               => $request->get('keep_releases', 10),
            'wildcard_domain'             => $request->get('wildcard_domain', 0),
            'zerotime_deployment'         => $request->get('zerotime_deployment', 0),
            'user_repository_provider_id' => $request->get('user_repository_provider_id'),
        ]);

        if ($request->has('servers')) {
            $changes = $site->servers()->sync($request->get('servers', []));

            foreach ($changes['attached'] as $serverID) {
                $this->dispatch(
                    (new CreateSite(Server::findOrFail($serverID), $site))->onQueue(config('queue.channels.server_commands'))
                );
            }

            foreach ($changes['detached'] as $serverID) {
                dd('site needs to be deleted');
            }
        }

        return response()->json($site);
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
        return response()->json(
            Site::findOrFail($id)->delete()
        );
    }

    /**
     * Deploys a site.
     * @param DeploySiteRequest $request
     */
    public function deploy(DeploySiteRequest $request, $siteId)
    {
        $site = Site::with('provisionedServers')->findOrFail($siteId);

        if ($site->provisionedServers->count()) {
            $this->dispatch(
                (new DeploySite($site))->onQueue(config('queue.channels.server_commands'))
            );
        }
    }

    /**
     * Rollbacks a site.
     * @param DeploySiteRequest $request
     */
    public function rollback(DeploySiteRequest $request, $siteId)
    {
        $site = Site::with('provisionedServers')->findOrFail($siteId);

        if ($site->provisionedServers->count()) {
            $this->dispatch(
                (new DeploySite($site, SiteDeployment::findOrFail($request->get('siteDeployment'))))->onQueue(config('queue.channels.server_commands'))
            );
        }
    }

    /**
     * @param SiteServerFeatureRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSiteServerFeatures(SiteServerFeatureRequest $request, $id)
    {
        $site = Site::findOrFail($id);

        return response()->json(
            $site->update([
                'server_features' => $request->get('services'),
            ])
        );
    }

    /**
     * Restarts a sites servers.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartServer($siteId)
    {
        $site = Site::findOrFail($siteId);

        $this->runOnServer(function () use ($site) {
            foreach ($site->provisionedServers as $server) {
                $this->serverService->restartServer($server);
            }
        });

        return $this->remoteResponse();
    }

    /**
     * Restart the sites web services.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartWebServices($siteId)
    {
        $site = Site::findOrFail($siteId);

        $this->runOnServer(function () use ($site) {
            foreach ($site->provisionedServers as $server) {
                $this->serverService->restartWebServices($server);
            }
        });

        return $this->remoteResponse();
    }

    /**
     * Restarts the sites databases.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartDatabases($siteId)
    {
        $site = Site::findOrFail($siteId);

        $this->runOnServer(function () use ($site) {
            foreach ($site->provisionedServers as $server) {
                $this->serverService->restartDatabase($server);
            }
        });

        return $this->remoteResponse();
    }

    /**
     * Restarts the sites worker services.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restartWorkerServices($siteId)
    {
        $site = Site::findOrFail($siteId);

        $this->runOnServer(function () use ($site) {
            foreach ($site->provisionedServers as $server) {
                $this->serverService->restartWorkers($server);
            }
        });

        return $this->remoteResponse();
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshSshKeys($siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->public_ssh_key = null;
        $this->repositoryService->importSshKey($site);

        return response()->json($site);
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshDeployKey($siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->update([
            'hash' => create_redis_hash()
        ]);

        if($site->automatic_deployment_id) {
            $this->repositoryService->deleteDeployHook($site);
            $this->repositoryService->createDeployHook($site);
        }

        return response()->json($site);
    }
}
