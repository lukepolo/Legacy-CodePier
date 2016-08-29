<?php

namespace App\Services\Server\ProvisionSystems;

class WebService
{
    public function installNginx()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx');

        $this->remoteTaskService->run('rm /etc/nginx/sites-enabled/default');
        $this->remoteTaskService->run('rm /etc/nginx/sites-available/default');

        $this->remoteTaskService->run('sed -i "s/user www-data;/user codepier;/" /etc/nginx/nginx.conf');
        $this->remoteTaskService->run('sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf');

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf');

        $this->remoteTaskService->run('openssl dhparam -out /etc/nginx/dhparam.pem 2048');

        $this->remoteTaskService->run('echo "fastcgi_param HTTP_PROXY \"\";" >> /etc/nginx/fastcgi_params');

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');
    }

    public function installCertBot()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y letsencrypt');
    }

    /**
     * @param Server $server
     * @param string $user
     * @return array
     */
    public function restartWebServices(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');

        return $this->remoteTaskService->getErrors();
    }

}