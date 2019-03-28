# CodePier

## Installation

### Homestead

At this time it is recommended to install CodePier under the `codepier.test`
domain in your system, even if your preferences may differ.
For the purposes of configuration, the following values are assumed, but yours may vary:

- `domain`: `codepier.test`
- `homestead IP`: `192.168.10.10`
  The text will have placeholders, for example `[domain]` or `[homestead IP]`, which you should replace with your values.

1. Edit `/etc/hosts`, add the following lines:

   ```sh
   192.168.10.10    codepier.test
   192.168.10.10    app.codepier.test
   192.168.10.10    stats.codepier.test
   192.168.10.10    lifeline.codepier.test
   192.168.10.10    provision.codepier.test
   ```

2. Set up `.env`. First copy `.env.example`, then fill in missing values from Luke. Also set the following items:

   ```env
    APP_ENV=local
    APP_KEY=<make-your-own>
    APP_DEBUG=true
    APP_URL=http://app.codepier.test
    APP_STATS_URL=http://stats.codepier.test
    APP_LIFELINE_URL=http://lifeline.codepier.test
    APP_PROVISION_URL=http://provision.codepier.test
    APP_ACME_DNS=http://dns.codepier.io
    APP_DOWN_WHITELIST_IPS=
    APP_FORCE_HTTPS=false
    APP_PUBLIC_URL=http://codepier.test

    LOG_CHANNEL=stack

    SESSION_COOKIE=codepier
    SESSION_SECURE_COOKIE=false
    SESSION_DOMAIN=.codepier.test

    DB_CONNECTION=mysql
    DB_HOST=192.168.10.10
    DB_DATABASE=codepier
    DB_USERNAME=homestead
    DB_PASSWORD=secret

    BROADCAST_DRIVER=redis
    CACHE_DRIVER=redis
    SESSION_DRIVER=redis
    QUEUE_DRIVER=redis

    REDIS_HOST=192.168.10.10
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    MAIL_DRIVER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=<set-up-your-own>
    MAIL_PASSWORD=<set-up-your-own>
    MAIL_ENCRYPTION=null

    MAILCHIMP_APIKEY=<get-from-luke>
    MAILCHIMP_LIST_ID=<get-from-luke>

    OAUTH_GITHUB_CLIENT_ID=<get-from-luke>
    OAUTH_GITHUB_SECRET_ID=<get-from-luke>
    OAUTH_GITHUB_CALLBACK=http://app.codepier.test/provider/github/callback

    OAUTH_BITBUCKET_CLIENT_ID=<get-from-luke>
    OAUTH_BITBUCKET_SECRET_ID=<get-from-luke>
    OAUTH_BITBUCKET_CALLBACK=http://app.codepier.test/provider/bitbucket/callback

    GITLAB_KEY=<get-from-luke>
    GITLAB_SECRET=<get-from-luke>
    GITLAB_REDIRECT_URI=http://app.codepier.test/provider/gitlab/callback
    GITLAB_INSTANCE_URI=https://gitlab.com/

    STRIPE_KEY=<get-from-luke>
    STRIPE_SECRET=<get-from-luke>

    SLACK_DOMAIN=codepier
    SLACK_KEY=<get-from-luke>
    SLACK_SECRET=<get-from-luke>
    SLACK_REDIRECT_URI=http://app.codepier.test/provider/slack/callback
    SLACK_HORIZON_NOTIFICATIONS=

    PUSHER_APP_KEY=<get-from-luke>
    PUSHER_APP_SECRET=
    PUSHER_APP_ID=<get-from-luke>

    LARAVEL_ECHO_SERVER_AUTH_HOST=http://app.codepier.test
    LARAVEL_ECHO_SERVER_HOST=192.168.10.10
    LARAVEL_ECHO_SERVER_PORT=6001
    LARAVEL_ECHO_SERVER_DEBUG=true

    # DO_SPACES_KEY=<get-from-luke>
    # DO_SPACES_SECRET=<get-from-luke>
    # DO_SPACES_REGION=nyc3
    # DO_SPACES_BUCKET=codepier-east-space-1

    SENTRY_JS=
    SENTRY_DSN=

    DEV_SSH_KEY="<add-your-public-key>"
   ```

3. Set up site configuration in Homestead.yaml:

   ```yml
   - map: [domain]
       to: /home/vagrant/Sites/CodePier/public
       php: "7.2"
   ```

4. Copy `laravel-echo-server.example` to `laravel-echo-server.json`.
5. Install composer dependencies: `composer install`. **DO NOT RUN COMPOSER UPDATE**, as that can break functionality.
6. Create application key: `php artisan key:generate`
7. Migrate database: `php artisan migrate:fresh --seed`
8. Install NVM:
   - follow instructions: https://github.com/creationix/nvm
   ```sh
   npm install --global yarn
   ```
9. Build assets:
   ```sh
   yarn install
   yarn run dev
   ```
10. Start Laravel Echo server: `yarn run echo`.
11. Set yourself as admin: open database, change `role` in your `users` record to `admin`.
12. Run Laravel Echo from within Homestead:
    ```sh
    homestead ssh
    cd Sites/CodePier
    yarn run echo
    ```
13. Log into your dev site: `http://codepier.test`.

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
