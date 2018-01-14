<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Services\Systems\ServiceConstructorTrait;

class PHPSettings
{
    use ServiceConstructorTrait;

    /**
     * @description Max File Upload in Megabytes (MB)
     */
    public function uploadSize($maxSize = '250', $postMaxSize = '250') {
        $phpVersion = $this->server->getLanguages()['PHP']['version'];

        $this->connectToServer();

        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'upload_max_filesize', 'upload_max_filesize='.$maxSize.'M');
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'post_max_size', 'post_max_size='.$postMaxSize.'M');

        $nginxConfig = '/etc/nginx/nginx.conf';

        if ($this->remoteTaskService->doesFileHaveLine($nginxConfig, 'client_max_body_size')) {
            $this->remoteTaskService->updateText($nginxConfig, 'client_max_body_size', 'client_max_body_size '.$data->params['max size'].'m;');
        } else {
            $this->remoteTaskService->findTextAndAppend($nginxConfig, 'http {', 'client_max_body_size '.$data->params['max size'].'m;');
        }

        $this->restartWebServices();
    }

    /**
     * @description Max File Upload in Megabytes (MB)
     */
    public function maxMemory($maxMemory = '250')
    {
        $phpVersion = $this->server->getLanguages()['PHP']['version'];

        $this->connectToServer();

        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'memory_limit', 'upload_max_filesize='.$maxMemory.'M');

        $this->restartWebServices();
    }

//    /**
//     * @description Manually optimize OPCache
//     */
//    public function OpCache()
//    {
//        dump('opcache');
//    }
}
