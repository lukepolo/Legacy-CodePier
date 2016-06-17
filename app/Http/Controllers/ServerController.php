<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Requests;
use App\Jobs\CreateServer;
use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\ServerSshKey;

/**
 * Class ServerController
 * @package App\Http\Controllers
 */
class ServerController extends Controller
{
    public $serverService;

    /**
     * ServerController constructor.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

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
        $this->dispatch((new CreateServer(
            ServerProvider::findorFail(\Request::get('server_provider_id')),
            \Auth::user(),
            \Request::get('name'),
            \Request::except(['_token', 'service'])
        ))->onQueue('server_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }

    /**
     * Installs a SSH key onto a server
     *
     * @param $serverID
     */
    public function postInstallSshKey($serverID)
    {
        $serverSshKey = ServerSshKey::create([
            'server_id' => $serverID,
            'name' => str_replace(' ', '_', \Request::get('name')),
            'ssh_key' => \Request::get('ssh_key')
        ]);

        $this->serverService->installSshKey(Server::findOrFail($serverID), $serverSshKey->ssh_key);

        return back()->with('success', 'You added an ssh key');
    }

    /**
     * Removes a SSH key on a server
     *
     * @param $serverID
     */
    public function getRemoveSshKey($serverID, $serverSshKeyId)
    {
        $serverSshKey = ServerSshKey::findOrFail($serverSshKeyId);

        $this->serverService->removeSshKey(Server::findOrFail($serverID), $serverSshKey->ssh_key);

        $serverSshKey->delete();

        return back()->with('success', 'You removed an ssh key');
    }
}
