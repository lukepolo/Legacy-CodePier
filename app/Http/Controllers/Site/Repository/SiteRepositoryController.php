<?php

namespace App\Http\Controllers\Site\Repository;

use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteRepositoryRequest;
use App\Models\Site\Site;

class SiteRepositoryController extends Controller
{
    private $serverService;
    private $repositoryService;

    /**
     * SiteController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService                         $serverService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(ServerService $serverService, RepositoryService $repositoryService)
    {
        $this->serverService = $serverService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteRepositoryRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteRepositoryRequest $request, $siteId)
    {
        $repository = $request->get('repository');

        $site = Site::with('server')->findOrFail($siteId);

        $site->update([
            'repository'                  => $repository,
            'branch'                      => $request->get('branch'),
            'zerotime_deployment'         => $request->get('zerotime_deployment'),
            'user_repository_provider_id' => $request->get('user_repository_provider_id'),
        ]);

        $this->repositoryService->importSshKeyIfPrivate($site);

        return response()->json($site);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($siteId, $id)
    {
        $site = Site::with('server')->findOrFail($id);

        $this->serverService->removeFolder($site->server, '/home/codepier/'.$site->domain, 'codepier');
        $this->serverService->createFolder($site->server, '/home/codepier/'.$site->domain, 'codepier');

        // TODO
        dd('NEEDS TO BE FIXED TO REMOVE ANYTHING FORM ALL SERVERS');

        $site->repository = null;
        $site->branch = null;
        $site->save();

        return response()->json($site);
    }
}
