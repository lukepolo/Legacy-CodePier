<?php

namespace App\Events\Server;

use function event;
use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Queue\SerializesModels;

class UpdateServerConfigurations
{
    use SerializesModels;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        event(new UpdateServerFiles($server, $site, $siteCommand));
        event(new UpdateServerSchemas($server, $site, $siteCommand));
        event(new UpdateServerSshKeys($server, $site, $siteCommand));
        event(new UpdateServerWorkers($server, $site, $siteCommand));
        event(new UpdateServerCronJobs($server, $site, $siteCommand));
        event(new UpdateServerFirewallRules($server, $site, $siteCommand));
        event(new UpdateServerSslCertificates($server, $site, $siteCommand));
        event(new UpdateServerLanguageSettings($server, $site, $siteCommand));
        event(new UpdateServerEnvironmentVariables($server, $site, $siteCommand));
    }
}
