<?php

namespace App\Http\Controllers\Server;

use App\Traits\SystemFiles;
use App\Models\Server\Server;
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
        $feature = $request->get('feature');
        $service = $request->get('service');

        /** @var Server $server */
        $server = Server::findOrFail($serverId);

        if ($this->serverFeatureService->installFeature($server, $service, $feature, $request->get('parameters', []))) {
            $server = $server->fresh();
            return response()->json($server->server_features);
        }

        return response()->json('It is currently being installed', 409);
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
}
