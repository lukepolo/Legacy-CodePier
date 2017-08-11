<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\ServiceConstructorTrait;

class NodeService
{
    use ServiceConstructorTrait;

    /**
     * @description NodeJs is a JavaScript runtime built on Chrome's V8 JavaScript engine. Node.js uses an event-driven, non-blocking I/O model that makes it lightweight and efficient. Node.js' package ecosystem, npm, is the largest ecosystem of open source libraries in the world.
     *
     * @options v6.11.1, v8.2.1
     * @multiple false
     *
     * @param string $version
     */
    public function installNodeJs($version = 'v6.11.1')
    {
        $this->connectToServer();

        $this->remoteTaskService->run('curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.2/install.sh | bash');

        $this->remoteTaskService->appendTextToFile('/home/codepier/.bashrc', '
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion
');

        $this->remoteTaskService->appendTextToFile('/root/.bashrc', '
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion
');

        $this->remoteTaskService->run('nvm install '.$version);
        $this->remoteTaskService->run('nvm alias default '.$version);

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
