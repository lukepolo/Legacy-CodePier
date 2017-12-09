<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\HTML\Frameworks;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_16_04\WebService;

class SinglePageApplication
{
    use ServiceConstructorTrait;

    public static $files = [
        '.env',
    ];

    public $suggestedFeatures = [

    ];

    public static $cronJobs = [

    ];

    public function getNginxConfig(Site $site)
    {
        return '
    location / {
        include '.WebService::NGINX_SERVER_FILES.'/'.$site->domain.'/root-location/*;
        try_files $uri /index.html =404;
    }
        ';
    }
}
