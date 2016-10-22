<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\ServerProvisionStep;

/**
 * Class ServerProvisionStepsController.
 */
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
        return response()->json(ServerProvisionStep::where('server_id', $serverId)->get());
    }
}
