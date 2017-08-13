<?php

namespace App\Http\Controllers\Server\Providers\Amazon;

use App\Http\Controllers\Controller;
use App\Models\Server\Provider\ServerProvider;
use App\Contracts\Server\ServerServiceContract as ServerService;

class AmazonServerFeaturesController extends Controller
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
            ServerProvider::with('serverFeatures')->where('provider_name', AmazonController::AMAZON)->firstOrFail()->serverFeatures
        );
    }
}
