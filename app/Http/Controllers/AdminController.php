<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Requests;
use App\Models\ServerProvider;

/**
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    private $serverService;

    /**
     * AdminController constructor.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('admin.index');
    }

    public function getServerOptionsAndRegions($serverProviderID)
    {
        $serverProvider = ServerProvider::findOrFail($serverProviderID);
        $this->serverService->getServerOptions($serverProvider);
        $this->serverService->getServerRegions($serverProvider);

        return back()->with('success', 'You have retrieved the server options and regions.');
    }
}
