<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Jobs\CreateServer;
use App\Models\Server;
use App\Models\ServerProvider;
use Illuminate\Http\Request;

/**
 * Class ServerController
 *
 * @package App\Http\Controllers\Server
 */
class ServerSiteController extends Controller
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
     * Display the specified resource.
     *
     * @param $serverId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($serverId, $id)
    {
        return response()->json(Server::with(['sites' => function($query) {
            $query->with([
                'activeSSL',
                'daemons',
                'settings',
                'userRepositoryProvider'
            ]);
        }])->findOrFail($id)->sites);
    }
}
