<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\CreateServer;
use App\Models\Server;

/**
 * Class ServerController
 * @package App\Http\Controllers
 */
class ServerController extends Controller
{
    /**
     * Shows the servers information
     *
     * @param $serverID
     *
     * @return mixed
     */
    public function getServer($serverID)
    {
        return view('server.index', [
            'server' => Server::with('sites')->findOrFail($serverID)
        ]);
    }

    /**
     * Creates a new server for the user
     *
     * @return mixed
     */
    public function postCreateServer()
    {
        $this->dispatch((new CreateServer(\Request::get('service'), \Auth::user(), \Request::get('name'),
            \Request::except(['_token', 'service'])))->onQueue('server_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }
}
