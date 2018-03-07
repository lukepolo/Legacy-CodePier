<?php

namespace App\Http\Controllers\Server\Providers\Vultr;

use App\Http\Controllers\Controller;
use App\Models\User\UserServerProvider;
use App\Models\Server\Provider\ServerProvider;
use App\Http\Requests\Server\ServerProviderRequest;
use App\Contracts\Server\ServerServiceContract as ServerService;

class VultrController extends Controller
{
    const VULTR = 'vultr';

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
        return $this->serverService->getServerProviderUser(ServerProvider::where('provider_name', self::VULTR)->firstOrFail());
    }

    public function store(ServerProviderRequest $request)
    {
        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'user_id'       => \Auth::user()->id,
            'account' => $request->get('account'),
            'token'         => $request->get('token'),
            'server_provider_id' => ServerProvider::where('provider_name', self::VULTR)->first()->id,
        ]);

        $userServerProvider->save();

        $userServerProvider->restore();

        return response()->json('OK');
    }
}
