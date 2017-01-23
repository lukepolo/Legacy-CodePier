<?php

namespace App\Services\Buoys;

use App\Traits\Buoys\BuoyTrait;
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
        $this->remoteTaskService->run('git clone https://github.com/getsentry/onpremise');
        $this->remoteTaskService->run('cd onpremise && make make build');

        $this->remoteTaskService->run('docker run \
            --detach \
            --name sentry-redis \
            redis:3.2-alpine
        ');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run \
            --detach \
            --name sentry-postgres \
            --env POSTGRES_PASSWORD=secret \
            --env POSTGRES_USER=sentry \
            postgres:9.5
        ');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run \
            --detach \
            --name sentry-smtp \
            tianon/exim4
        ');

        $this->getContainerId();

        $envVariables['SENTRY_SECRET_KEY'] = $this->remoteTaskService->run('docker run \
            --rm sentry-onpremise \
            config generate-secret-key',
            true
        );

        $envVariables = [
            'SENTRY_ADMIN_USERNAME' => $this->server->user->email,
            'SENTRY_ADMIN_PASSWORD' => str_random(),
            'SENTRY_ADMIN_EMAIL' => $this->server->user->email,
            'SENTRY_ALLOW_REGISTRATION' => false,
        ];

        // TODO - setup buoy mail

        foreach ($envVariables as $variable => $value) {
            $this->remoteTaskService->appendTextToFile('~/.bashrc', "$variable=$value");
            $this->remoteTaskService->appendTextToFile('/etc/environment', "$variable=$value");
        }

        $this->remoteTaskService->run('docker run \
            --rm \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            --env SENTRY_SECRET_KEY=$SENTRY_SECRET \
            -it sentry-onpremise \
            upgrade
        ');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run -d \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            --env SENTRY_SECRET_KEY=$SENTRY_SECRET \
            --name sentry-web-01 \
            -p '.$ports[0].':9000 \
            sentry-onpremise \
            run web
        ');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run  \
            -d \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            --name sentry-worker-01 \
            --env SENTRY_SECRET_KEY=$SENTRY_SECRET \
            sentry-onpremise \
            run worker
        ');

        $this->getContainerId();

        $this->remoteTaskService->run('docker run -d \
            --link sentry-redis:redis \
            --link sentry-postgres:postgres \
            --link sentry-smtp:smtp \
            --name sentry-cron \
            --env SENTRY_SECRET_KEY=$SENTRY_SECRET \
            sentry-onpremise \
            run cron
        ');

        $this->getContainerId();

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
