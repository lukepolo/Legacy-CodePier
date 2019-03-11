# CodePier

## Installation

### Homestead

For the purposes of configuration, the following values are assumed, but yours may vary:

- `domain`: `codepier.test`
- `homestead IP`: `192.168.10.10`
  The text will have placeholders, for example `[domain]` or `[homestead IP]`, which you should replace with your values.

1. Set up `.env`. Get values from Luke. Also set the following items:

   ```env
   APP_URL=http://app.[domain]
   APP_STATS_URL=http://stats.[domain]
   APP_LIFELINE_URL=http://lifeline.[domain]
   APP_PROVISION_URL=http://provision.[domain]
   APP_PUBLIC_URL=http://[domain]
   SESSION_DOMAIN=.[domain]
   DB_HOST=[homestead IP]
   DB_USERNAME=homestead
   DB_PASSWORD=secret
   REDIS_HOST=[homestead IP]
   OAUTH_GITHUB_CALLBACK=http://[domain]/provider/github/callback
   OAUTH_BITBUCKET_CALLBACK=http://[domain]/provider/bitbucket/callback
   GITLAB_REDIRECT_URI=http://[domain]/provider/gitlab/callback
   SLACK_REDIRECT_URI=http://[domain]/provider/slack/callback
   LARAVEL_ECHO_SERVER_AUTH_HOST=[homestead IP]
   LARAVEL_ECHO_SERVER_HOST=[homestead IP]
   ```

2. Set up site configuration in Homestead.yaml:

   ```yml
   - map: [domain]
       to: /home/vagrant/Sites/CodePier/public
       php: "7.2"
   ```

3. Copy `laravel-echo-server.example` to `laravel-echo-server.json` and configure as follows:
   1. Set `auth-host` to `[domain]`.
   2. Set `redis > host` `[homestead IP]`.
4. Install composer dependencies: `composer install`. **DO NOT RUN COMPOSER UPDATE**, as that can break functionality.
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
11. Run Laravel Echo from within Homestead:
    ```sh
    homestead ssh
    cd Sites/CodePier
    yarn run echo
    ```
12. Log into your dev site.

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
