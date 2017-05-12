<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Services\Systems\ServiceConstructorTrait;

class PHPSettings
{
    use ServiceConstructorTrait;

    /**
     * @description Max File Upload in Megabytes (MB)
     *
     * @params max size
     * @params post max size
     */
    public function uploadSize($data)
    {
        $phpVersion = $this->server->getLanguages()['PHP']['version'];

        $this->connectToServer();
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'upload_max_filesize', 'upload_max_filesize='.$data->params['max size'].'M');
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'post_max_size', 'post_max_size='.$data->params['post max size'].'M');

        $nginxConfig = '/etc/nginx/nginx.conf';

        if ($this->remoteTaskService->doesFileHaveLine($nginxConfig, 'client_max_body_size')) {
            $this->remoteTaskService->updateText($nginxConfig, 'client_max_body_size', 'client_max_body_size '.$data->params['max size'].'m;');
        } else {
            $this->remoteTaskService->findTextAndAppend($nginxConfig, 'http', 'client_max_body_size '.$data->params['max size'].'m;');
        }

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
