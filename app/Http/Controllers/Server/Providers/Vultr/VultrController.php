<?php

namespace App\Http\Controllers\Server\Providers\Vultr;

use Vultr\Exception\ApiException;
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

        try {
            $this->serverService->getServerProviderUser($userServerProvider);
        } catch (ApiException $e) {
            $userServerProvider->delete();
            return response()->json('We were unable to verify your API credentials, please make sure the token you supplied is correct', 400);
        } catch (\Exception $e) {
            $userServerProvider->delete();
            return response()->json('An unexpected error occurred, please try again', 400);
        }

        return response()->json('OK');
    }
}
