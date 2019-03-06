# CodePier

## Installation

### Homestead

1. Set up `.env`. Get values from Luke.
2. Set up site in Homestead.yaml:
   ```yml
   - map: cp.dev.genealabs.com
       to: /home/vagrant/Sites/CodePier/public
       php: "7.2"
   ```
3. Copy `laravel-echo-server.example` to `laravel-echo-server.json` and configure appropriately.
4. Install composer dependencies: `composer udpate`.
5. Create application key: `php artisan key:generate`
6. Migrate database: `php artisan migrate:fresh --seed`
7. Install NVM:
   - follow instructions: https://github.com/creationix/nvm
   ```sh
   npm install --global yarn
   ```
8. Build assets:
   ```sh
   yarn install
   yarn run dev
   ```
9. Start Laravel Echo server: `yarn run echo`.
10. Set yourself as admin: open database, change `role` in your `users` record to `admin`.

### With Valet

1. ## Copy .env_example
   ## -
1. We need to setup domains

```bash
    valet link codepier.test
    valet link app.codepier.test
```

2. Composer Install
3. NVM
   This allows you to change node / npm versions on the fly :
   - [Installing NVM](https://github.com/creationix/nvm)
   - Set your default node version
   - `bash nvm alias default {VESRION}`
   - npm install --global yarn
4. Installing Node Modules
   - nvm use
   - yarn install
5. Look at package.json to see commands to build
   - npm run watch
6. Running laravel echo
   - npm run echo
7. Start Workers
   - php artisan horizon
