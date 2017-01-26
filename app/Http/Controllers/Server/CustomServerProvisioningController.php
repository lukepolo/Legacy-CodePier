<?php

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Services\RemoteTaskService;
use App\Http\Controllers\Controller;
use App\Jobs\Server\CheckServerStatus;
use App\Models\Server\ProvisioningKey;

class CustomServerProvisioningController extends Controller
{
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->middleware('auth.provisioning-key')->except('provision');

        $this->remoteTaskService = $remoteTaskService;
    }

    public function provision()
    {
        return response()->download('provision.sh');
    }

    public function callback(Request $request)
    {
        $key = ProvisioningKey::findByKey($request->route('provisioning_key'));

        $server_id = $key->server->id;

        $server = Server::findOrFail($server_id);

        if (empty($server->public_ssh_key) || empty($server->private_ssh_key)) {
            $sshKey = $this->remoteTaskService->createSshKey();

            $server->public_ssh_key = $sshKey['publickey'];
            $server->private_ssh_key = $sshKey['privatekey'];
            $server->save();
        }

        $server->update([
            'ip' => $request->get('ip'),
        ]);
    }

    public function returnPublicKey(Request $request)
    {
        $provisioningKey = ProvisioningKey::find($request->route('provisioning_key'));

        $server = $provisioningKey->server;

        return $server->public_ssh_key;
    }

    public function returnPrivateKey(Request $request)
    {
        $provisioningKey = ProvisioningKey::find($request->route('provisioning_key'));
        $server = $provisioningKey->server;

        return $server->private_ssh_key;
    }

    public function end(Request $request)
    {
        $provisioningKey = ProvisioningKey::find($request->route('provisioning_key'));

        $provisioningKey->used = true;

        $provisioningKey->update();

        $server = $provisioningKey->server;

        dispatch(
            (new CheckServerStatus($server, true))->delay(5)->onQueue(env('SERVER_COMMAND_QUEUE'))
        );
    }
}
