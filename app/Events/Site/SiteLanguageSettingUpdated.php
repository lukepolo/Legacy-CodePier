<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\LanguageSetting;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
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
        $availableServers = $site->filterServersByType([
            SystemService::WEB_SERVER,
            SystemService::FULL_STACK_SERVER,
        ]);

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $languageSetting, 'Updating');

            foreach ($availableServers as $server) {
                dispatch(
                    (new UpdateServerLanguageSetting($server, $languageSetting, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
