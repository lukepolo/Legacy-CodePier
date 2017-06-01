<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\Ruby\Frameworks;

use App\Services\AbstractService;

class RubyOnRails extends AbstractService
{
    public static $files = [
        'config/secrets.yml',
    ];

    public $suggestedFeatures = [
        'Languages\Ruby\Frameworks\RubyOnRails' => [
            'RubyOnRails',
        ],
    ];

    public static $cronJobs = [
    ];

    public function installRubyOnRails()
    {
        $this->connectToServer();
        $this->remoteTaskService->run('gem update --system && gem install rails bundler --no-ri --no-rdoc');
    }

    public function getNginxConfig()
    {
    }
}
