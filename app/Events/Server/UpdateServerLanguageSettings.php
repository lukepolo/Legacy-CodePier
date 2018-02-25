<?php

namespace App\Events\Server;

use App\Jobs\Server\UpdateServerLanguageSetting;
use App\Models\Command;
use App\Models\LanguageSetting;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Services\Systems\SystemService;
use Illuminate\Queue\SerializesModels;

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
     * @param Server  $server
     * @param Site    $site
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
                SystemService::WEB_SERVER == $this->serverType ||
                SystemService::WORKER_SERVER == $this->serverType ||
                SystemService::FULL_STACK_SERVER == $this->serverType
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
