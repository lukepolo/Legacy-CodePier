<?php

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Jobs\Site\FixSiteServerConfigurations;
use App\Http\Requests\Server\ServerPrivateIpRequest;

class ServerPrivateIpAddressController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ServerPrivateIpRequest $request, $id)
    {
        $server = Server::findOrFail($id);

        $server->update([
            'private_ips' => $request->get('ip_addresses')
        ]);

        foreach ($server->sites as $site) {
            dispatch(
                (new FixSiteServerConfigurations($site))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        }

        return response()->json($server);
    }
}
