<?php

namespace App\Services\Buoys;

use App\Traits\Buoys\BuoyTrait;
use App\Notifications\BuoyInstall;
use App\Contracts\Buoys\BuoyContract;

class SentryBuoy implements BuoyContract
{
    use BuoyTrait;

    /**
     * @buoy-title Sentry
     * @buoy-enabled 1
     *
     * @description Sentry’s real-time error tracking gives you insight into production deployments and information to reproduce and fix crashes.
     * @category Services
     *
     * @param array $ports
     * @param array $options
     *
     * @buoy-ports Sentry Entry:9000:9000
     *
     * @return array Conatiner Ids
     */
    public function install($ports, $options)
    {
        $this->remoteTaskService->removeDirectory('onpremise');
        $this->remoteTaskService->run('git clone https://github.com/getsentry/onpremise');

        $this->remoteTaskService->updateText('~/onpremise/Dockerfile', 'FROM', 'FROM sentry:8.22-onbuild');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install make');

        $this->remoteTaskService->run('cd onpremise && mkdir -p /data/{sentry,postgres} && make build');

        $secretKey = str_random(32);
        $this->remoteTaskService->removeLineByText('~/.bashrc', 'SENTRY_SECRET_KEY');
        $this->remoteTaskService->removeLineByText('/etc/environment', 'SENTRY_SECRET_KEY');
        $this->remoteTaskService->appendTextToFile('~/.bashrc', "SENTRY_SECRET_KEY='$secretKey'");
        $this->remoteTaskService->appendTextToFile('/etc/environment', "SENTRY_SECRET_KEY='$secretKey'");

        $this->remoteTaskService->run('docker run -d \
            --name sentry-redis \
            redis:3.2-alpine');

        $this->remoteTaskService->run('docker run -d \
            --name sentry-postgres \
            --env POSTGRES_PASSWORD=secret \
            --env POSTGRES_USER=sentry \
            -v /data/postgres:/var/lib/postgresql/data \
            postgres:9.5');

        $this->remoteTaskService->run('docker run -d \
            --name sentry-smtp \
            tianon/exim4');

        $this->remoteTaskService->ssh($this->server, 'codepier');
        $this->remoteTaskService->ssh($this->server);

        $this->remoteTaskService->run('docker run -d \
            --rm \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            -e SENTRY_SECRET_KEY=$SENTRY_SECRET_KEY \
            -it sentry-onpremise \
            upgrade');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run --restart=always -d \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            --name sentry-worker-01 \
            --env SENTRY_SECRET_KEY=$SENTRY_SECRET_KEY \
            sentry-onpremise \
            run worker');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run --restart=always -d \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            --name sentry-cron \
            --env SENTRY_SECRET_KEY=$SENTRY_SECRET_KEY \
            sentry-onpremise \
            run cron');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run --restart=always -d \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            --env SENTRY_SECRET_KEY=$SENTRY_SECRET_KEY \
            --env SENTRY_ADMIN_USERNAME=$SENTRY_ADMIN_USERNAME \
            --env SENTRY_ADMIN_PASSWORD=$SENTRY_ADMIN_PASSWORD \
            --env SENTRY_ADMIN_EMAIL=$SENTRY_ADMIN_EMAIL \
            --env GITHUB_APP_ID=$GITHUB_APP_ID \
            --env GITHUB_API_SECRET=$GITHUB_API_SECRET \
            --name sentry-web-01 \
            -v /data/sentry:/var/lib/sentry/files \
            -p ' . $ports[0] . ':9000 \
            sentry-onpremise \
            run web');

        $this->getContainerId();

        $this->remoteTaskService->ssh($this->server, 'codepier');
        $this->remoteTaskService->appendTextToFile('~/.bashrc', 'alias createSentryUser="docker run -it --rm -e SENTRY_SECRET_KEY=$SENTRY_SECRET_KEY --link sentry-redis:redis --link sentry-postgres:postgres sentry-onpremise createuser"');

        $this->server->notify(new BuoyInstall('Sentry Login Details', [
            'Additional Steps' => 'Sentry is currently migrating its databases. Once you can access your sentry buoy, please ssh into your server ('.$this->server->ip.') and run "createSentryUser"',
            'You can access your sentry server via ' => $this->server->ip.":$ports[0]",
        ]));

        $this->openPorts($this->server, $ports, 'sentry');

        return $this->containerIds;
    }

    /**
     * When a buoy is set to a domain we must gather the web config.
     * return string.
     */
    public function nginxConfig()
    {
    }

    /**
     * When a buoy is set to a domain we must gather the web config.
     * return string.
     */
    public function apacheConfig()
    {
    }
}
