<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Jobs\Server\ProvisionServer;
use App\Events\Server\ServerProvisionStatusChanged;

class ServerProvisionStepsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->currentProvisioningStep()
        );
    }

    /**
     * Starts the provisioning process again.
     *
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($serverId)
    {
        $server = Server::with(['provisionSteps'])->findOrFail($serverId);

        dispatch(
            (new ProvisionServer($server))
                ->onQueue(config('queue.channels.server_provisioning'))
        );

        $currentStep = $server->currentProvisioningStep();

        $currentStep->failed = 0;
        $currentStep->log = null;
        $currentStep->save();

        broadcast(new ServerProvisionStatusChanged($server, $currentStep->step, $server->provisioningProgress()));

        return response()->json($currentStep);
    }
}
