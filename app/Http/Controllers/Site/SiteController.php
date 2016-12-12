<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Jobs\Site\CreateSite;
use App\Jobs\Site\DeploySite;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Models\Site\SiteFirewallRule;
use App\Http\Requests\Site\SiteRequest;
use App\Http\Requests\Site\DeploySiteRequest;
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

        SiteFirewallRule::create([
            'site_id'   => $site->id,
            'description' => 'HTTP',
            'port'        => '80',
            'from_ip'     => null,
        ]);

        SiteFirewallRule::create([
            'site_id'   => $site->id,
            'description' => 'HTTPS',
            'port'        => '443',
            'from_ip'     => null,
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
            Site::with('servers')->findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SiteRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRequest $request, $id)
    {
        $site = Site::findOrFail($id);

        $site->update([
            'branch'                      => $request->get('branch'),
            'domain'                      => $request->get('domainless') == true ? 'default' : $request->get('domain'),
            'name'                        => $request->get('domain'),
            'pile_id'                     => $request->get('pile_id'),
            'framework'                   => $request->get('framework'),
            'repository'                  => $request->get('repository'),
            'web_directory'               => $request->get('web_directory'),
            'wildcard_domain'             => (int) $request->get('wildcard_domain'),
            'zerotime_deployment'         => true,
            'user_repository_provider_id' => $request->get('user_repository_provider_id'),
            'type'                        => $request->get('type'),
        ]);

        $this->repositoryService->importSshKeyIfPrivate($site);

        if ($request->has('servers')) {
            $changes = $site->servers()->sync($request->get('servers', []));

            foreach ($changes['attached'] as $serverID) {
                $this->dispatch(
                    (new CreateSite(Server::findOrFail($serverID), $site))->onQueue(env('SERVER_COMMAND_QUEUE'))
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
    public function deploy(DeploySiteRequest $request)
    {
        $site = Site::with('servers')->findOrFail($request->get('site'));

        $this->dispatch(
            (new DeploySite($site))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );
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
}
