<?php

namespace App\Services\Server\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Models\Server;
use App\Models\Site;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;
use phpseclib\Net\SSH2;

/**
 * Class SiteService
 * @package App\Services
 */
class SiteService implements SiteServiceContract
{
    protected $remoteTaskService;

    public $deploymentServices = [
        'php' => DeploymentServices\PHP::class
    ];
    /**
     * SiteService constructor.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * Creates a site on the server
     *
     * @param Server $server
     * @param string $domain
     * @return bool
     */
    public function create(Server $server, $domain = 'default')
    {
        $this->remoteTaskService->ssh($server->ip);

        if ($this->remoteTaskService->run('
cat > /etc/nginx/sites-enabled/'.$domain.' <<    \'EOF\'

    # codeier CONFIG (DOT NOT REMOVE!)
    #include codeier-conf/'.$domain.'/before/*;

    server {
        listen 80;
        server_name '.$domain.';
        root /home/codepier/'.$domain.'/current/public;

        index index.html index.htm index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        access_log off;
        error_log  /var/log/nginx/'.$domain.'-error.log error;

        sendfile off;

        client_max_body_size 100m;

        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            fastcgi_intercept_errors off;
            fastcgi_buffer_size 16k;
            fastcgi_buffers 4 16k;
            fastcgi_connect_timeout 300;
            fastcgi_send_timeout 300;
            fastcgi_read_timeout 300;
        }

        location ~ /\.ht {
            deny all;
        }
    }

    # codeier CONFIG (DOT NOT REMOVE!)
    #include codeier-conf/'.$domain.'/after/*;
EOF
echo "Wrote" ')
        ) {

            $this->remoteTaskService->run('service nginx restart');
            Site::create([
                'domain' => $domain,
                'server_id' => $server->id,
                'wildcard_domain' => false,
                'zerotime_deployment' => false,
                'user_id' => $server->user_id,
                'path' => '/home/codepier/' . $domain,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Deploys a site on the server
     *
     * @param Server $server
     * @param Site $site
     * @param bool $zeroDownTime
     *
     * @return bool
     */
    public function deploy(Server $server, Site $site, $zeroDownTime = true)
    {
        $deploymentService = $this->getDeploymentService($server, $site);
        $deploymentService->updateRepository();
        $deploymentService->installVendorPackages();
        $deploymentService->runMigrations();
        $deploymentService->setupFolders();
        $deploymentService->cleanup();

        $this->remoteTaskService->ssh($server->ip);
        $this->remoteTaskService->run('service nginx restart');
        dd('done');
    }

    public function getFile(Server $server, $filePath)
    {
        $key = new RSA();
        $key->setPassword(env('SSH_KEY_PASSWORD'));
        $key->loadKey(file_get_contents('/home/vagrant/.ssh/id_rsa'));

        $ssh = new SFTP($server->ip);

        if (!$ssh->login('root', $key)) {
            exit('Login Failed');
        }

        if($contents = $ssh->get($filePath)) {
            return $contents;
        }

        return null;
    }

    private function getDeploymentService(Server $server, Site $site) {
        $deploymentService = 'php';
        return new $this->deploymentServices[$deploymentService]($this->remoteTaskService, $server, $site);
    }
}