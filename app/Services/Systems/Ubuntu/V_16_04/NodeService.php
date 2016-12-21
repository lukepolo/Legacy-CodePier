<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\ServiceConstructorTrait;

class NodeService
{
    use ServiceConstructorTrait;

    /**
     *  @description NodeJs is a JavaScript runtime built on Chrome's V8 JavaScript engine. Node.js uses an event-driven, non-blocking I/O model that makes it lightweight and efficient. Node.js' package ecosystem, npm, is the largest ecosystem of open source libraries in the world.
     */
    public function installNodeJs()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs npm');
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
