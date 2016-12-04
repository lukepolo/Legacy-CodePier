<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Jobs\Server\ProvisionServer;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Http\Requests\Server\ServerProvisioningStepsRequest;

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
     * @param ServerProvisioningStepsRequest $request
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ServerProvisioningStepsRequest $request, $serverId)
    {
        $server = Server::with(['provisionSteps'])->findOrFail($serverId);

        dispatch(new ProvisionServer($server));

        $currentStep = $server->currentProvisioningStep();

        $currentStep->failed = 0;
        $currentStep->log = null;
        $currentStep->save();

        event(new ServerProvisionStatusChanged($server, $currentStep->step, $server->provisioningProgress()));

        return response()->json($currentStep);
    }
}
