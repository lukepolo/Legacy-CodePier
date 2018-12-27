<?php

namespace App\Services\Systems\Ubuntu\V_18_04;

use App\Services\Systems\ServiceConstructorTrait;

class NodeService
{
    use ServiceConstructorTrait;

    /**
     * @description NodeJs is a JavaScript runtime built on Chrome's V8 JavaScript engine. Node.js uses an event-driven, non-blocking I/O model that makes it lightweight and efficient. Node.js' package ecosystem, npm, is the largest ecosystem of open source libraries in the world.
     *
     * @options 6.14.4, 8.12.0, 10.13.0, 11.1.0
     * @multiple false
     *
     * @param string $version
     */
    public function installNodeJs($version = '10.13.0')
    {
        $this->connectToServer();

        $this->remoteTaskService->run('git clone https://github.com/creationix/nvm.git /opt/.nvm');
        $this->remoteTaskService->run('cd /opt/.nvm && git checkout `git describe --abbrev=0 --tags --match "v[0-9]*" $(git rev-list --tags --max-count=1)`');
        $this->remoteTaskService->run('chown -R codepier:codepier /opt/.nvm');

        $this->remoteTaskService->prependTextToFile('/etc/bash.bashrc', '
export NVM_DIR=/opt/.nvm
[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"  # This loads nvm
');

        $this->connectToServer('codepier');

        $this->remoteTaskService->run('nvm install ' . $version);
        $this->remoteTaskService->run('nvm alias default ' . $version);

        $this->connectToServer("root");

        $this->remoteTaskService->run('NVM_DIR=$(which npm) && ln -s $NVM_DIR /usr/bin/npm');
        $this->remoteTaskService->run('NODE_DIR=$(which node) && ln -s $NODE_DIR /usr/bin/node');
    }

    /**
     *  @description Yarn is a fast, reliable and secure dependency management tool for NodeJs
     */
    public function installYarn()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('sudo apt-key adv --fetch-keys http://dl.yarnpkg.com/debian/pubkey.gpg');
        $this->remoteTaskService->run('echo "deb http://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list');
        $this->remoteTaskService->run('sudo apt-get update');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y yarn');
    }

    /**
     *  @description Bower is a dependency management tool for javascript and css packages
     */
    public function installBower()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('npm install -g bower');
    }

    /**
     *  @description Gulp is a toolkit that helps you automate painful or time-consuming tasks in your development workflow.
     */
    public function installGulp()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('npm install -g gulp');
    }
}
