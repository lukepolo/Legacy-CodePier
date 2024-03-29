<?php

namespace App\Http\Controllers\Server\Providers\Linode;

use App\Http\Controllers\Controller;
use App\Models\User\UserServerProvider;
use App\Exceptions\LinodeInvalidAccount;
use App\Models\Server\Provider\ServerProvider;
use App\Http\Requests\Server\ServerProviderRequest;
use App\Contracts\Server\ServerServiceContract as ServerService;

class LinodeController extends Controller
{
    const LINODE = 'linode';

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

    public function store(ServerProviderRequest $request)
    {
        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'user_id'       => \Auth::user()->id,
            'account' => $request->get('account'),
            'token'         => $request->get('token'),
            'token_secret'   => $request->get('secret_token'),
            'server_provider_id' => ServerProvider::where('provider_name', self::LINODE)->first()->id,
        ]);

        $userServerProvider->save();

        $userServerProvider->restore();

        try {
            $this->serverService->getServerProviderUser($userServerProvider);
        } catch (LinodeInvalidAccount $e) {
            $userServerProvider->delete();
            return response()->json('We were unable to verify your API credentials, please make sure the token you supplied is correct', 400);
        } catch (\Exception $e) {
            $userServerProvider->delete();
            return response()->json('An unexpected error occurred, please try again', 400);
        }

        return response()->json('OK');
    }
}
