<?php

namespace App\Http\Controllers\Server\Providers\Linode;

use App\Http\Controllers\Controller;
use App\Models\Server\Provider\ServerProvider;
use App\Contracts\Server\ServerServiceContract as ServerService;

class LinodeServerRegionsController extends Controller
{
    private $serverService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = ServerProvider::with(['serverRegions' => function ($query) {
            $query->orderBy('name');
        }])->where('provider_name', LinodeController::LINODE)->firstOrFail()->serverRegions;

        if ($regions->isEmpty()) {
            return $this->store();
        }

        return response()->json($regions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return response()->json(
            $this->serverService->getServerRegions(ServerProvider::where('provider_name', LinodeController::LINODE)->firstOrFail())
        );
    }
}
