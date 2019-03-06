<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Services\Systems\ServiceConstructorTrait;

class PHPSettings
{
    use ServiceConstructorTrait;

    /**
     * @description Max File Upload in Megabytes (MB)
     */
    public function uploadSize($maxSize = '250', $postMaxSize = '250')
    {
        $phpVersion = $this->server->getLanguages()['PHP']['version'];

        $this->connectToServer();

        $phpFpmFile = "/etc/php/$phpVersion/fpm/php.ini";
        $uploadMaxFileSize = 'upload_max_filesize='.$maxSize.'M';

        $this->remoteTaskService->updateText($phpFpmFile, 'post_max_size', 'post_max_size='.$postMaxSize.'M');

        if ($this->remoteTaskService->doesFileHaveLine($phpFpmFile, 'upload_max_filesize')) {
            $this->remoteTaskService->updateText($phpFpmFile, 'upload_max_filesize', $uploadMaxFileSize);
        } else {
            $this->remoteTaskService->findTextAndAppend($phpFpmFile, 'post_max_size', $uploadMaxFileSize);
        }

        $nginxConfig = '/etc/nginx/nginx.conf';
        $clientMaxBodySize = 'client_max_body_size '.$maxSize.'m;';

        if ($this->remoteTaskService->doesFileHaveLine($nginxConfig, 'client_max_body_size')) {
            $this->remoteTaskService->updateText($nginxConfig, 'client_max_body_size', $clientMaxBodySize);
        } else {
            $this->remoteTaskService->findTextAndAppend($nginxConfig, 'http ', $clientMaxBodySize);
        }

        $this->restartWebServices();
    }

    /**
     * @description Max Memory in Megabytes (MB)
     */
    public function maxMemory($maxMemory = '250')
    {
        $phpVersion = $this->server->getLanguages()['PHP']['version'];

        $this->connectToServer();

        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'memory_limit', 'memory_limit='.$maxMemory.'M');

        $this->restartWebServices();
    }

    /**
     * @description OpCache Settings - Options should be in Megabytes (MB)
     */
    public function OpCache($memoryConsumption = 512, $maxAcceleratedFiles = 7963, $internedStringsBuffer = 64)
    {
        $phpVersion = $this->server->getLanguages()['PHP']['version'];

        $this->connectToServer();

        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'opcache.enable', 'opcache.enable=1');
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'opcache.memory_consumption', "opcache.memory_consumption=$memoryConsumption");
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'opcache.interned_strings_buffer', "opcache.interned_strings_buffer=$internedStringsBuffer");
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'opcache.max_accelerated_files', "opcache.max_accelerated_files=$maxAcceleratedFiles");
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'opcache.validate_timestamps', 'opcache.validate_timestamps=0');
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'opcache.save_comments', 'opcache.save_comments=1');
        $this->remoteTaskService->updateText("/etc/php/$phpVersion/fpm/php.ini", 'opcache.fast_shutdown', 'opcache.fast_shutdown=1');

        $this->restartWebServices();
    }
}
