<?php

namespace App\Http\Controllers\Server;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use App\Models\Server\Server;
use App\Jobs\InstallServerFeature;
use App\Http\Controllers\Controller;
use App\Http\Requests\Server\ServerFeatureRequest;
use App\Contracts\Server\ServerFeatureServiceContract as ServerFeatureService;

class ServerFeatureController extends Controller
{
    use SystemFiles;

    private $serverFeatureService;

    /**
     * ServerFeatureController constructor.
     * @param \App\Services\Server\ServerFeatureService | ServerFeatureService $serverFeatureService
     */
    public function __construct(ServerFeatureService $serverFeatureService)
    {
        $this->serverFeatureService = $serverFeatureService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->server_features
        );
    }

    /**
     * @param ServerFeatureRequest $request
     * @param $serverId
     * @return mixed
     */
    public function store(ServerFeatureRequest $request, $serverId)
    {
        return $this->dispatch(
            (new InstallServerFeature(
                Server::findOrFail($serverId),
                $request->get('feature'),
                $request->get('service'),
                $request->get('parameters', [])
            ))->onQueue(env('SERVER_FEATURE_QUEUE'))
        );
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFeatures()
    {
        return response()->json($this->serverFeatureService->getBaseFeatures());
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLanguages()
    {
        return response()->json($this->serverFeatureService->getLanguages());
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFrameworks()
    {
        return response()->json($this->serverFeatureService->getFrameworks());
    }

    /**
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFiles($serverId)
    {
        return response()->json($this->serverFeatureService->getEditableFiles(Server::findOrFail($serverId)));
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFrameworkFiles($siteId)
    {
        return response()->json($this->serverFeatureService->getEditableFrameworkFiles(Site::findOrFail($siteId)));
    }
}
