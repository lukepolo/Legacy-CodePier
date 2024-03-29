<?php

namespace App\Events\Server;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Models\LanguageSetting;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\UpdateServerLanguageSetting;

class UpdateServerLanguageSettings
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $this->site->languageSettings->each(function (LanguageSetting $languageSettings) {
            if (! $languageSettings->hasServer($this->server) && (
                $this->serverType == SystemService::WEB_SERVER ||
                $this->serverType == SystemService::WORKER_SERVER ||
                $this->serverType == SystemService::FULL_STACK_SERVER
            )) {
                $this->updateServerLanguageSetting($languageSettings);
            }
        });
    }

    /**
     * @param LanguageSetting $languageSetting
     */
    private function updateServerLanguageSetting(LanguageSetting $languageSetting)
    {
        dispatch(
            (new UpdateServerLanguageSetting($this->server, $languageSetting, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
