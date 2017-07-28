<?php

namespace App\Http\Controllers\Server;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Jobs\Server\InstallServerFeature;
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
        $feature = $request->get('feature');
        $service = $request->get('service');

        $server = Server::findOrFail($serverId);
        $serverFeatures = $server->server_features;

        if ((
            ! isset($serverFeatures[$service][$feature]) ||
            ! isset($serverFeatures[$service][$feature]['enabled']) ||
            ! $serverFeatures[$service][$feature]['enabled']
        ) && (
            ! isset($serverFeatures[$service][$feature]['installing']) ||
            ! $serverFeatures[$service][$feature]['installing']
        )) {
            $serverFeatures[$service][$feature] = [
                'installing' => true,
            ];

            $this->dispatch(
                (new InstallServerFeature(
                    $server,
                    $feature,
                    $service,
                    $request->get('parameters', [])
                ))->onQueue(config('queue.channels.server_features'))
            );

            $server->update([
                'server_features' => $serverFeatures,
            ]);

            return response()->json($serverFeatures);
        }

        return response()->json('It is currntly being installed', 409);
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
        $languages = $this->serverFeatureService->getLanguages();

        // removing ruby for the time being
        unset($languages['Ruby']);

        return response()->json($languages);
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
