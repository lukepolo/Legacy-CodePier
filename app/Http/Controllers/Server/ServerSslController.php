<?php

namespace App\Http\Controllers\Server;

use App\Jobs\Server\SslCertificates\InstallServerSslCertificate;
use App\Jobs\Server\SslCertificates\RemoveServerSslCertificate;
use App\Models\Server\Server;
use App\Models\SslCertificate;
use App\Http\Requests\SslRequest;
use App\Http\Controllers\Controller;

class ServerSslController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->sslCertificates
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SslRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(SslRequest $request, $serverId)
    {
        $domains = $request->get('domains');
        $server = Server::with(['sslCertificates'])->findOrFail($serverId);

        switch ($type = $request->get('type')) {
            case 'existing':
                $sslCertificate = SslCertificate::create([
                    'domains' => $domains,
                    'type' => $request->get('type'),
                    'active' => false,
                    'key' => $request->get('private_key'),
                    'cert' => $request->get('certificate'),
                ]);
                break;
            default:
                throw new \Exception('Invalid SSL Type');
                break;
        }

        if (! $server->sslCertificates->where('id', $sslCertificate->id)->count()) {
            $server->sslCertificates()->attach($sslCertificate);
        }

        dispatch(
            (new InstallServerSslCertificate($server, $sslCertificate))->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        $server = Server::with('sslCertificates')->findOrFail($serverId);

        dispatch(
            (new RemoveServerSslCertificate($server, $server->sslCertificates->keyBy('id')->get($id)))->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json($server->sslCertificates()->detach($id));
    }
}
