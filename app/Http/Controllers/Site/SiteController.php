<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Jobs\Site\CreateSite;
use App\Jobs\Site\DeleteSite;
use App\Jobs\Site\DeploySite;
use App\Models\Server\Server;
use App\Events\Site\SiteRenamed;
use App\Models\Site\SiteDeployment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteRename;
use App\Events\Site\SiteRestartServers;
use App\Events\Site\SiteRestartWorkers;
use App\Http\Requests\Site\SiteRequest;
use App\Events\Site\SiteRestartDatabases;
use App\Events\Site\SiteUpdatedWebConfig;
use App\Events\Site\SiteRestartWebServices;
use App\Http\Requests\Site\DeploySiteRequest;
use App\Jobs\Site\FixSiteServerConfigurations;
use App\Http\Requests\Site\SiteRepositoryRequest;
use App\Http\Requests\Site\SiteServerFeatureRequest;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Models\User\UserRepositoryProvider;

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

        $this->middleware('checkMaxSites')
            ->except([
                'index',
                'show',
                'destroy',
            ]);

        $this->middleware('checkSiteCreationLimit')
            ->only([
                'store',
            ]);
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
        $isDomain = is_domain($request->get('domain'));

        $site = Site::create([
            'user_id'             => \Auth::user()->id,
            'domain'              => $isDomain ? $request->get('domain') : 'default',
            'pile_id'             => $request->get('pile_id'),
            'name'                => $request->get('domain'),
            'workflow'            => \Auth::user()->workflow ? null : [],
            'wildcard_domain'     => $request->get('wildcard_domain', 0),
            'keep_releases'       => 10,
            'zero_downtime_deployment' => true,
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

        $userRepositoryProvider =  UserRepositoryProvider::where('id', $request->get('user_repository_provider_id'))->first();

        $site->fill([
            'type'                        => $request->get('type'),
            'branch'                      => $request->get('branch'),
            'framework'                   => $request->get('framework'),
            'repository'                  => $request->get('repository'),
            'web_directory'               => $request->get('web_directory'),
            'user_repository_provider_id' => 0,
        ]);

        if (! empty($userRepositoryProvider)) {
            $site->fill([
                'user_repository_provider_id' => $userRepositoryProvider->id,
            ]);
        }

        if ($site->isDirty('web_directory') || $site->isDirty('type') || $site->isDirty('framework')) {
            event(new SiteUpdatedWebConfig($site));
        }

        $site->save();

        if ($request->has('servers')) {
            $changes = $site->servers()->sync($request->get('servers', []));

            foreach ($changes['attached'] as $server) {
                dispatch(
                    (new CreateSite(Server::findOrFail($server), $site))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }

            foreach ($changes['detached'] as $server) {
                (new DeleteSite(Server::findOrFail($server), $site))
                    ->onQueue(config('queue.channels.server_commands'));
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function deploy(DeploySiteRequest $request, $siteId)
    {
        $site = Site::with('provisionedServers')->findOrFail($siteId);

        if ($site->provisionedServers->count()) {
            $lastDeploymentStatus = $site->last_deployment_status;
            if (empty($lastDeploymentStatus) || $lastDeploymentStatus === SiteDeployment::FAILED || $lastDeploymentStatus === SiteDeployment::COMPLETED) {
                dispatch(
                    (new DeploySite($site))->onQueue(config('queue.channels.site_deployments'))
                );
            } else {
                return response()->json('You have a deployment currently running', 409);
            }
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
            dispatch(
                (new DeploySite($site, SiteDeployment::findOrFail($request->get('siteDeployment'))))
                    ->onQueue(config('queue.channels.site_deployments'))
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
        broadcast(new SiteRestartServers(Site::findOrFail($siteId)));

        return $this->remoteResponse('OK');
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
        broadcast(new SiteRestartWebServices(Site::findOrFail($siteId)));

        return $this->remoteResponse('OK');
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
        broadcast(new SiteRestartDatabases(Site::findOrFail($siteId)));

        return $this->remoteResponse('OK');
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
        broadcast(new SiteRestartWorkers(Site::findOrFail($siteId)));

        return $this->remoteResponse('OK');
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshPublicKey($siteId)
    {
        $site = Site::findOrFail($siteId);

        $this->repositoryService->generateNewSshKeys($site);
        $this->repositoryService->saveKeysToServer($site);

        return response()->json($site);
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshDeployKey($siteId)
    {
        $site = Site::findOrFail($siteId);

        do {
            $success = false;

            try {
                $site->update([
                    'hash' => unique_hash(),
                ]);
                $success = true;
            } catch (\Exception $e) {
                return response()->json($site);
            }
        } while (! $success);

        if (! empty($site->automatic_deployment_id)) {
            try {
                $this->repositoryService->deleteDeployHook($site);
                $this->repositoryService->createDeployHook($site);
            } catch (\Exception $e) {
                // its ok to fail here
            }
        }

        return response()->json($site);
    }

    /**
     * @param SiteRename $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(SiteRename $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $oldDomain = $site->domain;
        $newDomain = is_domain($request->get('domain')) ? $request->get('domain') : 'default';

        foreach ($site->files->filter(function ($value) {
            return $value->framework_file || $value->custom;
        }) as $siteFile) {
            $siteFile->file_path = str_replace($oldDomain, $newDomain, $siteFile->file_path);
            $siteFile->save();
        }

        $site->update([
            'domain' => $newDomain,
            'name' => $request->get('domain'),
            'wildcard_domain' => $request->get('wildcard_domain', 0),
        ]);

        if ($oldDomain !== $site->domain) {
            event(new SiteRenamed($site, $site->domain, $oldDomain));
        }

        return response()->json($site);
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function fixServerConfigurations($siteId)
    {
        $site = Site::findOrFail($siteId);

        dispatch(
            (new FixSiteServerConfigurations($site))
                ->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json('OK');
    }
}
