<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\ServerStat;
use App\Http\Controllers\Controller;

class ServerStatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(ServerStat::where('server_id', $serverId)->firstOrFail());
    }
}
