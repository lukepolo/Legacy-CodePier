<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Jobs\CreateServer;
use App\Models\Server;
use App\Models\ServerCronJob;
use App\Models\ServerDaemon;
use App\Models\ServerFirewallRule;
use App\Models\ServerNetworkRule;
use App\Models\ServerProvider;
use App\Models\ServerSshKey;
use Maknz\Slack\Client;

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
     * @param $serverID
     * @return mixed
     */
    public function getServer($serverID)
    {
        $server = Server::with('sites')->findOrFail($serverID);
        // TODO - move this into notifications once 5.3 is released
//        $client = new \Guzzle\Http\Client();
//
//        $response = $client->post('https://slack.com/api/chat.postMessage', [], [
//            'token' => \Auth::user()->userNotificationProviders->first()->token,
//            'channel' => 'general',
//            'text' => 'HI FROM CODE PIER!'
//        ])->send();



        return view('server.index', [
            'server' => $server,
            'servers' => Server::where('id', '!=', $serverID)->get(),
            'diskspace' => $this->serverService->checkDiskSpace($server)
        ]);
    }

    /**
     * Creates a new server for the user
     * @return mixed
     */
    public function postCreateServer()
    {
        $server = Server::create([
            'user_id' => \Auth::user()->id,
            'name' => \Request::get('name'),
            'server_provider_id' => (int)\Request::get('server_provider_id'),
            'status' => 'Queued For Creation',
            'progress' => '0',
            'options' => \Request::except(['_token', 'service', 'features']),
            'features' => array_keys(\Request::get('features'))
        ]);


        $this->dispatch(new CreateServer(
            ServerProvider::findorFail(\Request::get('server_provider_id')),
            $server
        ));
//            ->onQueue('server_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }

    /**
     * Adds a server network rule so they can talk to each other
     * @param $serverID
     * @return mixed
     */
    public function postAddServerNetworkRules($serverID)
    {
        $server = Server::findOrFail($serverID);
        $connectToServers = Server::whereIn('id', \Request::get('servers'))->whereDoesntHave('connectedServers')->get();

        foreach($connectToServers as $connectToServer) {

            ServerNetworkRule::create([
                'server_id' => $connectToServer->id,
                'connect_to_server_id' => $server->id,
            ]);

            $this->serverService->addServerNetworkRule($connectToServer, $server->ip);
        }

        $serverNetworkRules = ServerNetworkRule::with('server')->where('connect_to_server_id', $serverID)->whereNotIn('server_id',  \Request::get('servers', []))->get();

        foreach($serverNetworkRules as $serverNetworkRule) {
            $this->serverService->removeServerNetworkRule($serverNetworkRule->server, $server->ip);

            $serverNetworkRule->delete();
        }

        return redirect()->withSuccess('you have updated your server network rules');
    }

    /**
     * Archives a server
     * @param $serverID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getArchiveServer($serverID)
    {
        Server::findOrFail($serverID)->delete();

        return redirect()->action('LandingController@getIndex')->with('success', 'You have archived the server');
    }

    /**
     * Gets the list of archived servers
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getArchivedServers()
    {
        return view('server.archive_list', [
            'servers' => Server::onlyTrashed()->get()
        ]);
    }

    /**
     * Activates an arhcived server
     * @param $serverID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getActivateArchivedServer($serverID)
    {
        Server::onlyTrashed()->findOrFail($serverID)->restore();

        return redirect()->action('LandingController@getIndex')->with('success', 'You have restored the server');
    }

    /**
     * Installs a SSH key onto a server
     * @param $serverID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postInstallSshKey($serverID)
    {
        $server = Server::findOrFail($serverID);

        $serverSshKey = ServerSshKey::create([
            'server_id' => $serverID,
            'name' => \Request::get('name'),
            'ssh_key' => trim(\Request::get('ssh_key'))
        ]);

        $this->serverService->installSshKey($server, $serverSshKey->ssh_key);

        return back()->with('success', 'You added an ssh key');
    }

    /**
     * Removes a SSH key on a server
     * @param $serverID
     * @param $serverSshKeyId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRemoveSshKey($serverID, $serverSshKeyId)
    {
        $server = Server::findOrFail($serverID);

        $serverSshKey = ServerSshKey::findOrFail($serverSshKeyId);

        $this->serverService->removeSshKey($server, $serverSshKey->ssh_key);

        $serverSshKey->delete();

        return back()->with('success', 'You removed an ssh key');
    }

    /**
     * Installs a cron job on a server
     * @param $serverID
     * @return mixed
     */
    public function postInstallCronJob($serverID)
    {
        $this->serverService->installCron(Server::findOrFail($serverID), \Request::get('cron_timing').\Request::get('cron'));

        return back()->with('success', 'You added a cron job');
    }

    /**
     * Removes a cron job on a server
     * @param $serverID
     * @param $cronJobID
     * @return mixed
     */
    public function getRemoveCronJob($serverID, $cronJobID)
    {
        $this->serverService->removeCron(Server::findOrFail($serverID), ServerCronJob::findorFail($cronJobID));

        return back()->with('success', 'You removed a cron job');
    }

    /**
     * Adds a firewall rule to a server
     * @param $serverID
     * @return mixed
     */
    public function postAddFireWallRule($serverID)
    {
        $this->serverService->addFirewallRule(
            Server::findOrFail($serverID),
            \Request::get('port'),
            \Request::get('from_ip'),
            \Request::get('description'));

        return back()->with('success', 'You added a firewall rule');
    }

    /**
     * Removes a firewall rule to a server
     * @param $serverID
     * @param $serverFireWallID
     * @return mixed
     */
    public function getRemoveFireWallRule($serverID, $serverFireWallID)
    {
        $this->serverService->removeFirewallRule(Server::findOrFail($serverID),
            ServerFirewallRule::findOrFail($serverFireWallID));

        return back()->with('success', 'You removed a firewall rule');
    }

    /**
     * Adds a daemon to a server
     * @param $serverID
     * @return mixed
     */
    public function postAddDaemon($serverID)
    {
        $this->serverService->installDaemon(
            Server::findOrFail($serverID),
            \Request::get('command'),
            \Request::get('auto_start'),
            \Request::get('auto_restart'),
            \Request::get('user'),
            \Request::get('number_of_workers')
        );

        return back()->with('success', 'You installed a new daemon');

    }

    /**
     * Removes a daemon to a server
     * @param $serverID
     * @param $serverDaemonID
     * @return mixed
     */
    public function getRemoveDaemon($serverID, $serverDaemonID)
    {
        $this->serverService->removeDaemon(Server::findOrFail($serverID), ServerDaemon::findOrFail($serverDaemonID));

        return back()->with('success', 'You removed a daemon');
    }

    /**
     * Test the ssh connection
     * @param $serverID
     */
    public function getTestSshConnection($serverID)
    {
        $this->serverService->testSshConnection(Server::findOrFail($serverID));
    }

    /**
     * Restarts the web services on the server
     * @param $serverID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRestartWebServices($serverID)
    {
        $this->serverService->restartWebServices(Server::findOrFail($serverID));
        return back()->with('success', 'You have restarted the web server sercies');
    }

    /**
     * Restarts the entire server
     * @param $serverID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRestartServer($serverID)
    {
        $this->serverService->restartServer(Server::findOrFail($serverID));
        return back()->with('success', 'You have restarted the server');
    }

    /**
     * Restarts the datatbases
     * @param $serverID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRestartDatabase($serverID)
    {
        $this->serverService->restartDatabase(Server::findOrFail($serverID));
        return back()->with('success', 'You have restarted the databases');
    }

    /**
     * Restarts the workers
     * @param $serverID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRestartWorkers($serverID)
    {
        $this->serverService->restartWorkers(Server::findOrFail($serverID));
        return back()->with('success', 'You have restarted the servers workers');
    }

    /**
     * Gets a file from the server
     * @param $serverID
     * @return null|string
     * @throws \Exception
     */
    public function getFileFromServer($serverID)
    {
        if(!\Request::has('path')) {
            throw new \Exception('Must include the path parameter');
        }

        return $this->serverService->getFile(Server::findOrFail($serverID), \Request::get('path'));
    }

    public function postSaveFile($serverID)
    {
        if(!\Request::has('path')) {
            throw new \Exception('Must include the path parameter');
        }

        $this->serverService->saveFile(Server::findOrFail($serverID), \Request::get('path'), \Request::get('file'));

        return back()->with('success', 'You have saved updated the file');
    }

    public function postInstallBlackfire($serverID)
    {
        $this->serverService->installBlackFire(Server::findOrFail($serverID), \Request::get('server_id'), \Request::get('server_token'));

        return back()->with('success', 'You have saved updated the file');
    }
}
