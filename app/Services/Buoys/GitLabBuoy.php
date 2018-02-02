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
     * @buoy-ports GitLab SSH:2222:22
     * @buoy-ports GitLab Web:8008:80
     *
     * @return array Conatiner Ids
     */
    public function install($ports, $options)
    {
        $this->remoteTaskService->run('docker run -d \
            --name gitlab \
            --hostname '.$this->server->ip.' \
            --publish '.$ports[1].':'.$ports[1].' --publish '.$ports[0].':22 \
            --restart always \
            -v /data/gitlab/config:/etc/gitlab \
            -v /data/gitlab/logs:/var/log/gitlab \
            -v /data/gitlab/data:/var/opt/gitlab \
            gitlab/gitlab-ce:latest');

        $tries = 0;

        while ($tries < 50) {
            if (! $this->remoteTaskService->isFileEmpty('/data/gitlab/config/gitlab.rb')) {
                $this->remoteTaskService->updateText('/data/gitlab/config/gitlab.rb', "# external_url 'GENERATED_EXTERNAL_URL'", "external_url 'http://".$this->server->ip.':'.$ports[1]."'", true, '@');
                $this->remoteTaskService->updateText('/data/gitlab/config/gitlab.rb', "# gitlab_rails\['gitlab_shell_ssh_port'] = 22", "gitlab_rails['gitlab_shell_ssh_port'] = ".$ports[0], true);
                $this->remoteTaskService->updateText('/data/gitlab/config/gitlab.rb', "# gitlab_rails\['smtp_enable'] = true", "gitlab_rails['smtp_enable'] = true", true);
                break;
            }

            sleep(2);
            $tries++;
        }

        $this->server->notify(new BuoyInstall('GitLab Buoy Setup', [
            'Accessing Your Instance' => 'Your GitLab instance should be accessible shortly. Please navigate to: http://'.$this->server->ip.':'.$ports[1].'/ in order to access the web interface.',
            'You may perform GitLab SSH operations via' => $this->server->ip.':'.$ports[0],
            'Please Note' => 'If you intend for this instance to be an internal tool, please be sure to disable open registration in the admin area.',
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
