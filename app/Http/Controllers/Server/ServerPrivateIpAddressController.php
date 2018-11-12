<?php

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;

class ServerPrivateIpAddressController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $server = Server::findOrFail($id);

        $server->update([
            'private_ips' => $request->get('ip_addresses')
        ]);

        return response()->json($server);
    }
}
