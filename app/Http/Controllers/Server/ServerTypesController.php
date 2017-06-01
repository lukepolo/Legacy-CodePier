<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class ServerTypesController extends Controller
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
     * Gets the server types that we are able to provision.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            \App\Services\Server\ServerService::SERVER_TYPES
        );
    }
}
