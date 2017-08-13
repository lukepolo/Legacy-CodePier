<?php

namespace App\Http\Controllers\Server\Providers\Linode;

use App\Http\Controllers\Controller;
use App\Models\User\UserServerProvider;
use App\Models\Server\Provider\ServerProvider;
use App\Http\Requests\Server\ServerProviderRequest;

class LinodeController extends Controller
{
    const LINODE = 'linode';

    /**
     * @param ServerProviderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ServerProviderRequest $request)
    {
        $userId = \Auth::user()->id;

        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'server_provider_id' => ServerProvider::where('provider_name', self::LINODE)->first()->id,
            'provider_id'        => $userId,
        ]);

        $userServerProvider->fill([
            'user_id'       => $userId,
            'token'         => $request->get('token'),
            'token_secret'   => $request->get('secret_token'),
        ]);

        $userServerProvider->save();

        $userServerProvider->restore();

        return response()->json('OK');
    }
}
