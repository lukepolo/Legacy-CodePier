<?php

namespace App\Http\Controllers\Server\Providers\DigitalOcean;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Auth\OauthController;
use App\Http\Controllers\Controller;
use App\Models\Server\Provider\ServerProvider;

class DigitalOceanServerOptionsController extends Controller
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
     */
    public function index()
    {
        $options = ServerProvider::with('serverOptions')->where('provider_name', OauthController::DIGITAL_OCEAN)->first()->serverOptions;

        if ($options->isEmpty()) {
            return $this->store();
        }

        return response()->json($options);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return response()->json(
            $this->serverService->getServerOptions(ServerProvider::where('provider_name', OauthController::DIGITAL_OCEAN)->firstOrFail())
        );
    }
}
