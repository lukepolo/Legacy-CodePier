## CodePier


## Installing


### With Valet

1. Copy .env_example
    * 
    *
    *
1. We need to setup domains
```bash
    valet link codepier.test
    valet link app.codepier.test
```
2. Composer Install
3. NVM
This allows you to change node / npm versions on the fly :
   * [Installing NVM](https://github.com/creationix/nvm)
   * Set your default node version
   * ```bash nvm alias default {VESRION}```
   * npm install --global yarn
4. Installing Node Modules
    * nvm use
    * yarn install
5. Look at package.json to see commands to build
    * npm run watch 
6. Running laravel echo
    * npm run echo
7. Start Workers
    * php artisan horizon
