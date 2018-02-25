<?php

namespace App\Http\Controllers\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Http\Controllers\Controller;
use App\Jobs\Server\CheckServerStatus;
use App\Models\Server\ProvisioningKey;
use App\Models\Server\Server;
use Illuminate\Http\Request;

class CustomServerProvisioningController extends Controller
{
    /**
     * CustomServerProvisioningController constructor.
     *
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->middleware('auth.provisioning-key')->except([
            'getIp',
            'provision',
        ]);

        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function provision()
    {
        if ('production' == config('app.env')) {
            return response()->download('provision.sh');
        } else {
            return response()->download('provision.dev.sh');
        }
    }

    /**
     * @param Request $request
     *
     * @return null|string
     */
    public function getIp(Request $request)
    {
        return $request->getClientIp();
    }

    /**
     * @param Request $request
     */
    public function start(Request $request)
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

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function returnPublicKey(Request $request)
    {
        return ProvisioningKey::findOrFail($request->route('provisioning_key'))->server->public_ssh_key;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function returnPrivateKey(Request $request)
    {
        return $provisioningKey = ProvisioningKey::findOrFail($request->route('provisioning_key')->server->private_ssh_key);
    }

    /**
     * Webhook called by provisioning shell script when finished.
     *
     * @param Request $request
     */
    public function end(Request $request)
    {
        $provisioningKey = ProvisioningKey::findOrFail($request->route('provisioning_key'));

        $server = $provisioningKey->server;

        dispatch(
            (new CheckServerStatus($server, true))
                ->delay(5)
                ->onQueue(config('queue.channels.server_commands'))
        );

        $provisioningKey->delete();
    }
}
