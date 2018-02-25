@extends('layouts.public')

@section('content')
    So , you want to know what all of CodePier offers?  OOOK! Its a long list!

    <h1>Languages</h1>
    <li>
        <h3>PHP</h3>
        <ul>
            <li>Laravel</li>
            <li>Cake PHP</li>
            <li>Symfony</li>
            <li>Codeigniter</li>
        </ul>
        <h3>HTML - Supports Node / Javascript Deployments</h3>
        <ul>
            <li>SPA</li>
        </ul>
        <h3>Ruby - COMING SOON!</h3>
        <h3>Python - COMING SOON!</h3>
        <h3>GO - COMING SOON!</h3>
    </li>

    <h1>Account Features</h1>
    <ul>
        <li>Two-Factor Authentication</li>
        <li>Customizable Notification Settings for email and slack</li>
        <li>Global SSH Keys</li>
        <li>Teams - Coming Soon!</li>
        <li>API - Coming Soon!</li>
    </ul>


    <h1>Piles - Organization of Applications</h1>
    <ul>

    </ul>

    <h1>Repository Providers</h1>
    <ul>
        <li>Github</li>
        <li>GitLab</li>
        <li>BitBucket</li>
        <li>Custom - When using the site's SSH Key Provided</li>
    </ul>

    <h1>Server Providers</h1>
    <ul>
        <li>Digital Ocean</li>
        <li>Linode</li>
        <li>Vultr</li>
        <li>Custom - Any Ubuntu 16.04 Based System</li>
    </ul>

    <h1>Site Features</h1>
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

    <h1>Deployments</h1>
    <ul>
        <li>Auto Deploy</li>
        <li>1 Click Deploys</li>
        <li>Customizable Deployments</li>
        <li>Customized Deployment Scripts</li>
        <li>Rollbacks</li>
    </ul>

    <h2>Server Features</h2>
    <li>Secured by only allowing logins with SSH key authentication only</li>
    <li>Server Monitoring w/Notifications for : CPU , Memory, Disk Usage</li>

    <h1>Setup Features for Sites / Servers</h1>
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

    <h1>Installable Server Features</h1>
    <ul>
        <li>
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
        </li>
        <li>
            <h3>Firewall Features</h3>
            <ul>
                <li>Fail2ban</li>
            </ul>
        </li>
        <li>
            <h3>Moniotiring Features</h3>
            <ul>
                <li>Disk Monitoring Script </li>
                <li>Server Load Monitoring Script </li>
                <li>Memory Monitoring Script </li>
            </ul>
        </li>
        <li>
            <h3>Node Features</h3>
            <ul>
                <li>NodeJS with NVM</li>
                <li>Yarn</li>
                <li>Bower</li>
                <li>Gulp</li>
            </ul>
        </li>
        <li>
            <h3>OS Features</h3>
            <ul>
                <li>Swap</li>
                <li>Docker</li>
            </ul>
        </li>
        <li>
            <h3>Repository Features</h3>
            <ul>
                <li>Git</li>
            </ul>
        </li>
        <li>
            <h3>Web Features</h3>
            <ul>
                <li>CertBot</li>
                <li>Nginx</li>
            </ul>
        </li>
        <li>
            <h3>Worker Features</h3>
            <ul>
                <li>Beanstalk</li>
                <li>Supervisor</li>
            </ul>
        </li>
        <li>
            <h3>PHP Features</h3>
            <ul>
                <li>PHP 7.0+</li>
                <li>PHPFpm</li>
                <li>Composer</li>
                <li>Blackfire</li>
                <h3>Laravel Features</h3>
                <li>Enovy</li>
            </ul>
        </li>
    </ul>

    <h1>Server Distributions</h1>
    <ul>
        <li>Ubuntu 16.04 LTS</li>
        <li>Ubuntu 18.04 LTS - COMING SOON!</li>
    </ul>

    <h1>Events Bar</h1>
    <ul>
        <li>All Events are reactive, no refreshing needed!</li>
        <li>Dig deep into events, see errors and timing of events</li>
        <li>1440p Monitors get extra features for events bar!</li>
        <li>Can open events as a new window</li>
    </ul>

    <h1>Buoys (1 Click Installs)</h1>
    <ul>
        <li>Sentry</li>
        <li>Gitlab</li>
        <li>Elasticsearch</li>
    </ul>

    <h1>Misc Features</h1>
    <ul>
        <li>Bitts (custom shell scripts) - COMING SOON!</li>
        <li>Live Support with a Developer</li>
    </ul>

    There are many details missing from this list, but gives you the magnitude of time saving that it gives you!
    Please chat us up to suggest features / report bugs , we are very open to ideas and features!

@endsection

@push('scripts')

@endpush
