<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\LanguageSetting;
use App\Services\Systems\SystemService;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\UpdateServerLanguageSetting;

class SiteLanguageSettingUpdated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param LanguageSetting $languageSetting
     */
    public function __construct(Site $site, LanguageSetting $languageSetting)
    {
        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $languageSetting);

            foreach ($site->provisionedServers as $server) {

                $serverType = $server->type;

                if(
                    $serverType === SystemService::WORKER_SERVER ||
                    $serverType === SystemService::FULL_STACK_SERVER
                ) {
                    dispatch(
                        (new UpdateServerLanguageSetting($server, $languageSetting, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                    );
                }

            }
        }
    }
}
