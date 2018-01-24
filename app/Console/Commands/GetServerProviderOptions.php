<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Auth\OauthController;
use App\Models\Server\Provider\ServerProvider;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Server\Providers\Linode\LinodeController;

class GetServerProviderOptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:serverOptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets all the server options and regions';

    /**
     * Execute the console command.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return mixed
     */
    public function handle(ServerService $serverService)
    {
        \Auth::loginUsingId(1);
        $serverService->getServerOptions(ServerProvider::with('serverOptions')->where('provider_name', OauthController::DIGITAL_OCEAN)->firstOrFail());
//        $serverService->getServerRegions(ServerProvider::with('serverRegions')->where('provider_name', OauthController::DIGITAL_OCEAN)->firstOrFail());
//
//        $serverService->getServerOptions(ServerProvider::with('serverOptions')->where('provider_name', LinodeController::LINODE)->firstOrFail());
//        $serverService->getServerRegions(ServerProvider::with('serverRegions')->where('provider_name', LinodeController::LINODE)->firstOrFail());
    }
}
