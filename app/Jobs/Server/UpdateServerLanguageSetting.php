<?php

namespace App\Jobs\Server;

use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Models\LanguageSetting;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class UpdateServerLanguageSetting implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $languageSetting;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param LanguageSetting $languageSetting
     * @param Command $siteCommand
     */
    public function __construct(Server $server, LanguageSetting $languageSetting, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->languageSetting = $languageSetting;
        $this->makeCommand($server, $languageSetting, $siteCommand);
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->runLanguageSetting($this->server, $this->languageSetting);
        });

        if (! $this->wasSuccessful()) {
            throw new ServerCommandFailed($this->getCommandErrors());
        }
    }
}
