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
    public function getServer($serverID)
    {
        return view('server.index', [
            'server' => Server::findOrFail($serverID)
        ]);
    }

    public function postCreateServer()
    {
        $this->dispatch((new CreateServer(\Request::get('service'), \Auth::user(), \Request::except(['_token', 'service'])))->onQueue('server_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }
}
