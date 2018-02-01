<?php

namespace App\Services\Buoys;

use App\Traits\Buoys\BuoyTrait;
use App\Notifications\BuoyInstall;
use App\Contracts\Buoys\BuoyContract;

class GitLabBuoy implements BuoyContract
{
    use BuoyTrait;

    /**
     * @buoy-title GitLab
     * @buoy-enabled 1
     *
     * @description GitLab is a web-based Git repository manager.
     * @category Services
     *
     * @param array $ports
     * @param array $options
     *
     * @buoy-ports GitLab Web:8080:80
     * @buoy-ports GitLab SSH:2222:22
     *
     * @return array Conatiner Ids
     */
    public function install($ports, $options)
    {
        $this->remoteTaskService->run('docker run -d \
            --name gitlab \
            --hostname '.$this->server->ip.' \
            --publish 8080:80 --publish 2222:22 \
            --restart always \
            -v /data/gitlab/config:/etc/gitlab \
            -v /data/gitlab/logs:/var/log/gitlab \
            -v /data/gitlab/data:/var/opt/gitlab \
            gitlab/gitlab-ce:latest');

        $this->server->notify(new BuoyInstall('GitLab Buoy Setup', [
            'Accessing Your Instance' => 'Your GitLab instance should be accessible shortly. Please navigate to: http://'.$this->server->ip.':8080/ in order to access the web interface.',
            'Helpful Hint: You can perform GitLab SSH operations via ' => $this->server->ip.":2222",
        ]));

        $this->openPorts($this->server, $ports, 'GitLab');

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
