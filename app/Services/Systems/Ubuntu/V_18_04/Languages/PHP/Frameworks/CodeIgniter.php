<?php

namespace App\Services\Systems\Ubuntu\V_18_04\Languages\PHP\Frameworks;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_18_04\WebService;

class CodeIgniter
{
    use ServiceConstructorTrait;

    public static $files = [

    ];

    public $suggestedFeatures = [

    ];

    public static $cronJobs = [

    ];

    public function getNginxConfig(Site $site, $phpVersion)
    {
        return '
    location / {
        include '.WebService::NGINX_SERVER_FILES.'/'.$site->domain.'/root-location/*;
        # Check if a file or directory index file exists, else route it to index.php.
        try_files $uri $uri/ /index.php;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php'.$phpVersion.'-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        
        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP\'s OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }
';
    }
}
