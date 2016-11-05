<?php

namespace App\Http\Controllers\Server\Providers\DigitalOcean;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Auth\OauthController;
use App\Http\Controllers\Controller;
use App\Models\Server\Provider\ServerProvider;
use Illuminate\Http\Request;

class DigitalOceanServerRegionsController extends Controller
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
    public function index(Request $request)
    {
        return response()->json(
            ServerProvider::with(['serverRegions' => function ($query) {
                $query->orderBy('name');
            }])->where('provider_name', OauthController::DIGITAL_OCEAN)->firstOrFail()->serverRegions
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json(
            $this->serverService->getServerRegions(ServerProvider::with('serverRegions')->where('provider_name', OauthController::DIGITAL_OCEAN)->firstOrFail())
        );
    }
}
