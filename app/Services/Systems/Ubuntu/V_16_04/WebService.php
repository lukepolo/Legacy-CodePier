<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\SystemService;

class WebService
{
    use ServiceConstructorTrait;

    public static $files = [
        'installNginx' => [
            '/etc/nginx/nginx.conf',
        ]
    ];

//    public function installApache()
//    {
//    }

    public function installCertBot()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y letsencrypt');
    }

    public function installNginx()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx');

        $this->remoteTaskService->removeFile('/etc/nginx/sites-enabled/default');
        $this->remoteTaskService->removeFile('/etc/nginx/sites-available/default');

        $this->remoteTaskService->run('sed -i "s/user www-data;/user codepier;/" /etc/nginx/nginx.conf');
        $this->remoteTaskService->run('sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf');

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf');

        $this->remoteTaskService->writeToFile('/etc/nginx/dhparam.pem', env('DH_PARAM'));

        $this->remoteTaskService->run('echo "fastcgi_param HTTP_PROXY \"\";" >> /etc/nginx/fastcgi_params');

        $this->remoteTaskService->run('service nginx restart');

        $this->remoteTaskService->writeToFile('/etc/nginx/dhparam.pem',
            '-----BEGIN DH PARAMETERS-----
MIIBCAKCAQEA5M2MrvvA978Z4Zz6FBf/1CUZA3QcJyCUmeMwPVWBeTS9M3XJTYUY
Hr7UXZQtzWF5o3GLC2SAMzVVHGaJQDnruxBT5HLsneFpSZz5ntCq4tLLIE32dyYd
Vd/K+Mp1Cee3lw57iK/ZC/CfxoZ5qtWJ9/CRmfXwS8QMwmLl+pR8v5m0I4TqzgRM
1HEbY1YvgKNiy24HbOhr62Von27Fa8IpGVVhLjoL6VTNaGjh64vtbMZzp1Va9G5P
rPJFzPmaWrfBecGIEWEN77NLT8ieYpiLUw0s4PgnlM6Pijax/Z/YsqsZpN8nvmDc
gQw5FUmzayuEHRxRIy1uQ6qkPRThOrGQswIBAg==
-----END DH PARAMETERS-----');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service nginx restart');
    }
}
