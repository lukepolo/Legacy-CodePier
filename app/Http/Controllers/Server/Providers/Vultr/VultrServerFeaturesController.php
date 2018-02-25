<?php

namespace App\Http\Controllers\Server\Providers\Vultr;

use App\Http\Controllers\Controller;
use App\Models\Server\Provider\ServerProvider;
use App\Contracts\Server\ServerServiceContract as ServerService;

class VultrServerFeaturesController extends Controller
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
        return response()->json(
            ServerProvider::with('serverFeatures')->where('provider_name', VultrController::VULTR)->firstOrFail()->serverFeatures
        );
    }
}
