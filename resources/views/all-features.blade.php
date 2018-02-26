@extends('layouts.public')

@section('content')

    <section class="section">
        <div class="section--content">

            <h1>All Features</h1>
            <hr>

            <h2>Languages</h2>

            <ul>
                <h3>PHP</h3>
                <ul>
                    <li>Laravel</li>
                    <li>Cake PHP</li>
                    <li>Symfony</li>
                    <li>Codeigniter</li>
                </ul>
                <h3>HTML
                    <small>(Supports Node / Javascript Deployments)</small>
                </h3>
                <ul>
                    <li>SPA <small>(single page sites)</small></li>
                </ul>
            </ul>
            <p>Planning to support other lanauges such as Ruby, Python, and GO</p>

            <h2>Account Features</h2>
            <ul>
                <li>Two-Factor Authentication</li>
                <li>Customizable Notification Settings for Email and Slack</li>
                <li>Global SSH Keys</li>
                <li>Teams <em>- COMING SOON!</em></li>
                <li>API <em>- COMING SOON!</em></li>
            </ul>


            <h2>Piles
                <small>(Organization of Sites)</small>
            </h2>
            <ul>

            </ul>

            <h2>Repository Providers</h2>
            <ul>
                <li>Github</li>
                <li>GitLab</li>
                <li>BitBucket</li>
                <li>Custom <small>- When using the site's SSH key provided</small></li>
            </ul>

            <h2>Server Providers</h2>
            <ul>
                <li>Digital Ocean</li>
                <li>Linode</li>
                <li>Vultr</li>
                <li>Custom <small>- Any Ubuntu 16.04 based system</small></li>
            </ul>

            <h2>Site Features</h2>
            <ul>
                <li>Site Priority Design</li>
                <li>Database Backups <em>- COMING SOON!</em></li>
                <li>Lifelines <small>- URL's to be triggered to see the health of services</small></li>
                <li>Current DNS IP of a Record</li>
                <li>Custom Site Notifications for Slack</li>
                <li>Wildcard Domains</li>
                <li>Easy Renaming of Sites</li>
                <li>Site Files</li>
                <li>Restart Services <small>- web / databases / workers and daemons</small></li>
                <li>Provisioning Different Server Types <small>- full stack, web, database, worker and load balancer</small></li>
            </ul>

            <h2>Deployments</h2>
            <ul>
                <li>Auto Deploy</li>
                <li>1 Click Deployment</li>
                <li>Customizable Deployments</li>
                <li>Customized Deployment Scripts</li>
                <li>Rollbacks</li>
            </ul>

            <h2>Server Features</h2>
            <ul>
                <li>Secured by Bnly Allowing Logins with SSH Key Authentication</li>
                <li>Server Monitoring with Notifications for CPU, Memory and Disk Usage</li>
            </ul>

            <h2>Setup Features for Sites / Servers</h2>
            <ul>
                <li>Databases and Users</li>
                <li>Site SSH Keys</li>
                <li>Firewall Rules</li>
                <li>Free SSL Certificates</li>
                <li>Free Wildcard Certificates <em>- COMING SOON!</em></li>
                <li>Environment Variables</li>
                <li>Cron Jobs</li>
                <li>Daemons</li>
                <li>Workers</li>
                <li>Server Files</li>
                <li>Language Settings</li>
                <li>Server Features</li>
            </ul>

            <h2>Installable Server Features</h2>
            <ul>
                <h3>Database Features</h3>
                <ul>
                    <li>MariaDB</li>
                    <li>Memcached</li>
                    <li>MySQL</li>
                    <li>PostgresSQL</li>
                    <li>Redis</li>
                    <li>SQLite</li>
                    <li>MongoDB</li>
                </ul>
                <h3>Firewall Features</h3>
                <ul>
                    <li>Fail2ban</li>
                </ul>
                <h3>Monitoring Features</h3>
                <ul>
                    <li>Disk Monitoring Script</li>
                    <li>Server Load Monitoring Script</li>
                    <li>Memory Monitoring Script</li>
                </ul>
                <h3>Node Features</h3>
                <ul>
                    <li>NodeJS with NVM</li>
                    <li>Yarn</li>
                    <li>Bower</li>
                    <li>Gulp</li>
                </ul>
                <h3>OS Features</h3>
                <ul>
                    <li>Swap</li>
                    <li>Docker</li>
                </ul>
                <h3>Repository Features</h3>
                <ul>
                    <li>Git</li>
                </ul>
                <h3>Web Features</h3>
                <ul>
                    <li>CertBot</li>
                    <li>Nginx</li>
                </ul>
                <h3>Worker Features</h3>
                <ul>
                    <li>Beanstalk</li>
                    <li>Supervisor</li>
                </ul>
                <h3>PHP Features</h3>
                <ul>
                    <li>PHP 7.0+</li>
                    <li>PHP-FPM</li>
                    <li>Composer</li>
                    <li>Blackfire</li>
                    <br>
                    <h3>Laravel Features</h3>
                    <li>Enovy</li>
                </ul>

            </ul>

            <h2>Server Distributions</h2>
            <ul>
                <li>Ubuntu 16.04 LTS</li>
                <li>Ubuntu 18.04 LTS <em>- COMING SOON!</em></li>
            </ul>

            <h2>Events Bar</h2>
            <ul>
                <li>All events are reactive; no refreshing needed!</li>
                <li>Dig deep into events, see errors and timing.</li>
                <li>1440p Monitors allow for events to always be visible in sidebar.</li>
                <li>Can open events as a new window.</li>
            </ul>

            <h2>Buoys <small>(1 Click Installs)</small></h2>
            <ul>
                <li>Sentry</li>
                <li>Gitlab</li>
                <li>Elasticsearch</li>
            </ul>

            <h2>Misc. Features</h2>
            <ul>
                <li>Bitts <small>(custom shell scripts)</small> <em>- COMING SOON!</em></li>
                <li>Live Support with a Developer</li>
            </ul>

            <hr>
            <p>
                We are very open to ideas. Please contact us through the "get help" chat to suggest features or report bugs.
            </p>

        </div>
    </section>

@endsection

@push('scripts')

@endpush
