<?php

namespace App\Http\Controllers\Server\Providers\DigitalOcean;

use App\Http\Controllers\Controller;
use App\Models\User\UserServerProvider;
use App\Models\Server\Provider\ServerProvider;
use App\Http\Requests\Server\ServerProviderRequest;
use App\Contracts\Server\ServerServiceContract as ServerService;

class DigitalOceanController extends Controller
{
    const DIGITALOCEAN = 'digitalocean';

    private $serverService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->serverService->getServerProviderUser(ServerProvider::where('provider_name', self::DIGITALOCEAN)->firstOrFail());
    }

    public function store(ServerProviderRequest $request)
    {
        $userId = \Auth::user()->id;

        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'server_provider_id' => ServerProvider::where('provider_name', self::DIGITALOCEAN)->first()->id,
            'provider_id'        => $userId,
        ]);

        $userServerProvider->fill([
            'user_id'      => $userId,
            'token'        => $request->get('token'),
            'token_secret' => $request->get('secret_token'),
            'account'      => $request->get('account'),
        ]);

        $userServerProvider->save();

        $userServerProvider->restore();

        return response()->json('OK');
    }
}
