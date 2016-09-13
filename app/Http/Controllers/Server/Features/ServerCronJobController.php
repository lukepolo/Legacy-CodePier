<?php

namespace App\Http\Controllers\Server\Features;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerCronJob;
use Illuminate\Http\Request;

/**
 * Class ServerCronJobController.
 */
class ServerCronJobController extends Controller
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
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(Server::with('cronJobs')->findOrFail($request->get('server_id'))->cronJobs);
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
        $serverCronJob = ServerCronJob::create([
            'server_id' => $request->get('server_id'),
            'job'       => $request->get('cron_timing').' '.$request->get('cron'),
            'user'      => $request->get('user'),
        ]);

        $this->serverService->installCron($serverCronJob);

        return response()->json($serverCronJob);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(ServerCronJob::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->serverService->removeCron(ServerCronJob::findorFail($id));
    }
}
