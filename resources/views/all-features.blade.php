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
                    <li>SPA (single page applications)</li>
                </ul>
                <h3>Ruby - COMING SOON!</h3>
                <br>
                <h3>Python - COMING SOON!</h3>
                <br>
                <h3>GO - COMING SOON!</h3>
            </ul>

            <h2>Account Features</h2>
            <ul>
                <li>Two-Factor Authentication</li>
                <li>Customizable Notification Settings for email and slack</li>
                <li>Global SSH Keys</li>
                <li>Teams - Coming Soon!</li>
                <li>API - Coming Soon!</li>
            </ul>


            <h2>Piles
                <small>(Organization of Applications)</small>
            </h2>
            <ul>

            </ul>

            <h2>Repository Providers</h2>
            <ul>
                <li>Github</li>
                <li>GitLab</li>
                <li>BitBucket</li>
                <li>Custom - When using the site's SSH Key Provided</li>
            </ul>

            <h2>Server Providers</h2>
            <ul>
                <li>Digital Ocean</li>
                <li>Linode</li>
                <li>Vultr</li>
                <li>Custom - Any Ubuntu 16.04 Based System</li>
            </ul>

            <h2>Site Features</h2>
            <ul>
                <li>Site Priority Design</li>
                <li>Database Backups - COMING SOON!</li>
                <li>Lifelines - URL's to be triggered to see the health of services</li>
                <li>Current DNS IP of A record</li>
                <li>Custom site notifications for slack</li>
                <li>Wildcard Domains</li>
                <li>Easy renaming of sites</li>
                <li>Site Files</li>
                <li>Restart of services : web / databases / workers & daemons</li>
                <li>Provisioning of different server types : full stack, web, database, worker, load balancer</li>
            </ul>

            <h2>Deployments</h2>
            <ul>
                <li>Auto Deploy</li>
                <li>1 Click Deploys</li>
                <li>Customizable Deployments</li>
                <li>Customized Deployment Scripts</li>
                <li>Rollbacks</li>
            </ul>

            <h2>Server Features</h2>
            <ul>
                <li>Secured by only allowing logins with SSH key authentication only</li>
                <li>Server Monitoring w/Notifications for : CPU , Memory, Disk Usage</li>
            </ul>

            <h2>Setup Features for Sites / Servers</h2>
            <ul>
                <li>Databases and users</li>
                <li>Site SSH Keys</li>
                <li>Firewall Rules</li>
                <li>Free SSL Certificates</li>
                <li>Free Wildcard Certificates - COMING SOON!</li>
                <li>Environment Variables</li>
                <li>Cron Jobs</li>
                <li>Daemons</li>
                <li>Workers</li>
                <li>Server Files</li>
                <li>Language settings</li>
                <li>Server Features</li>
            </ul>

            <h2>Installable Server Features</h2>
            <ul>
                <h3>Datasbase Features</h3>
                <ul>
                    <li>MariaDB</li>
                    <li>Memcached</li>
                    <li>MySQL</li>
                    <li>PostgresSQL</li>
                    <li>Redis</li>
                    <li>SqlLite</li>
                    <li>MongoDB</li>
                </ul>
                <h3>Firewall Features</h3>
                <ul>
                    <li>Fail2ban</li>
                </ul>
                <h3>Moniotiring Features</h3>
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
                    <li>PHPFpm</li>
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
                <li>Ubuntu 18.04 LTS - COMING SOON!</li>
            </ul>

            <h2>Events Bar</h2>
            <ul>
                <li>All Events are reactive, no refreshing needed!</li>
                <li>Dig deep into events, see errors and timing of events</li>
                <li>1440p Monitors get extra features for events bar!</li>
                <li>Can open events as a new window</li>
            </ul>

            <h2>Buoys (1 Click Installs)</h2>
            <ul>
                <li>Sentry</li>
                <li>Gitlab</li>
                <li>Elasticsearch</li>
            </ul>

            <h2>Misc. Features</h2>
            <ul>
                <li>Bitts (custom shell scripts) - COMING SOON!</li>
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
