<?php

namespace App\Http\Controllers\Server\Providers\Amazon;

use App\Http\Controllers\Controller;
use App\Models\User\UserServerProvider;
use App\Models\Server\Provider\ServerProvider;
use App\Http\Requests\Server\ServerProviderRequest;

class AmazonController extends Controller
{
    const AMAZON = 'amazon';

    /**
     * @param ServerProviderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ServerProviderRequest $request)
    {
        $userId = \Auth::user()->id;

        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'server_provider_id' => ServerProvider::where('provider_name', self::AMAZON)->first()->id,
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
