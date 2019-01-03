@extends('layouts.public')

@section('content')

    <section class="section">
        <div class="section--content">
            <h1>January 2nd, 2019</h1>
            <hr>
            <ul>
               <li>Ubuntu 18.04 Servers Now Available</li>
               <li>New selector for choosing which server version you wish to use</li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>Progress now starts at 1% to give better feedback something is happening</li>
                <li>Fixed issues with Swift Installation</li>
                <li>Other Minor Bug Fixes</li>
            </ul>
        </div>
    </section>

    <section class="section">
        <div class="section--content">
            <h1>October 31st, 2018</h1>
            <hr>
            <ul>
                <li>Private IPS now are collected so we can improve security when opening ports.</li>
                <li>You can manage private IPs for servers in the server page</li>
                <li>New servers will take advantage of better NVM support</li>
                <li>DNS Lookups for wildcard SSL now perform on the name server your domain is using.</li>
                <li>Added new LTS node versions, and next version. (6.14.4 LTS, 8.12.0 LTS, 10.13.0 LTS, 11.1.0 Current)</li>
                <li>Added Vultr server features (IPV6, Private IPs, backups, DDOS protection)</li>
                <li>Easily see Private IP addresses from site overview pages</li>
                <li>We warn you if daemons / workers cannot be used in the sub nav of the site pages</li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>Fixed Postgres Database restart commands</li>
                <li>Fixed Postgres permission issues</li>
                <li>New Servers have improved performance tweaks</li>
                <li>SWAP now has a set of 2 GB, you can increase this still in the advanced options</li>
                <li>Load balancers wil now use private IPs of servers </li>
                <li>Firewall Rules will react better when adding a new server</li>
                <li>Worker / Daemons will not try to remove it knows the server does not have it installed</li>
                <li>And more!</li>
            </ul>
        </div>
    </section>

    <section class="section">
        <div class="section--content">
            <h1>September 1st, 2018</h1>
            <hr>
            <h2>New</h2>
            <ul>
                <h4>Vapor for Swift!</h4>
                <li>We have released our initial take on using Vapor! Give it a go and let us know if we missed anything.</li>
                <li>Discord Notification Provider! You can now use discord for your notifications!</li>
                <li>For subscribers we now check if they have an invalid / expiring SSL certificate daily! </li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>A Ton Of Bug Fixes</li>
                <li>Server provisioning should see a huge performance increase</li>
                <li>Fixed issues of installing some server features</li>
            </ul>
        </div>
    </section>

    <section class="section">
        <div class="section--content">
            <h1>March 22th, 2018</h1>
            <hr>
            <h2>New</h2>
            <ul>
                <li>Backups for Mongo / Mysql / MariaDB / Postgres is now in beta!</li>
                <li>Wildcard Certificates Available with automatic renewals for all DNS providers! Was no easy feat!</li>
                <li>Bitts are now available which are little code snippets that you can use to run on your server. There are future improvements to this system incoming over the next several releases</li>
            </ul>

            <h2>Changed</h2>
            <ul>
                <li>Let's Encrypt Package is replaced by cert-bot, you will not need to do anything</li>
                <li>SSL Certificates screen has been adjusted to a new UI workflow, these improvements will be coming to other areas of the app in the next several releases</li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>When updating language settings, we now invoke to refresh all server files so we can make certain they are in sync with UI.</li>
                <li>Switching between frameworks now replaces cronjobs and server files and framework files properly</li>
                <li>Misc backend fixes to make these a bit smoother for the user.</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>Fixed NGINX config containing `internal` directive for apps that need the use of it</li>
            </ul>
        </div>
    </section>

    <section class="section">
        <div class="section--content">
            <h1>March 7th, 2018</h1>
            <hr>
            <h2>New</h2>
            <ul>
                <li>Favicon now changes when there are running commands</li>
                <li>Server Providers are named : this gives you the flexibility to add multiple accounts of the same provider</li>
                <li>Deployments now allow you to set some tasks to be run after all deployments are successful!</li>
                <li>You can now change a site to a wildcard site on the site overview page</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>Moved "Generic PHP" to modern PHP, then added Legacy PHP option</li>
            </ul>

            <h2>Changed</h2>
            <ul>
                <li>Custom Provision script has been updated, and now checks which system your trying to provision and will fail if you don't have the correct system</li>
                <li>Moved the get help in the nav bar to a first class citizen!</li>
                <li>Removed oauth by digital ocean in favor of API tokens to make it uniform with the other providers and this allows you to know which account your Digital Ocean is linked to.</li>
            </ul>

            <h2>Fixed</h2>
            <ul>
                <li>2nd Auth now has a longer window from 1 minute to 1.5 minutes</li>
                <li>Long Server / Site names have been fixed</li>
                <li>Cron Job Maker has been improved to have the look and feel of the rest of the site</li>
                <li>Multiple sites of the same domain / name that would cause conflicts</li>
                <li>Fixed total calculations on multiple servers when deployments are completed</li>
                <li>Docked with CodePier image has been restored</li>
                <li>Multiple areas of the site had UI bugs have been fixed</li>
            </ul>

            <p>
                As always we have some things that are in the backend that cannot be seen, but just know that we are working to incorporate bigger things very soon!
            </p>
        </div>
    </section>

    <section class="section">
        <div class="section--content">
            <h1>February 18, 2018</h1>
            <hr>
            <h2>New</h2>
            <ul>
                <li>Announcements and change logs will show up in modals on login</li>
                <li>Open events bar into a new window for better accessibility</li>
                <li>New modal to help new users with the experience of CodePier</li>
                <li>New Server Provisioning Events so you can watch in your events bar while you provision a new
                    server
                </li>
                <li>Added some timings to deployments and provisions events</li>
            </ul>


            <ul>
                <h4>PHP</h4>
                <li>OpCache configuration is now under Language Settings</li>
            </ul>

            <h2>Changed</h2>
            <ul>
                <li>Support is now in the “gear” menu</li>
                <li>Piles has a new location to make it known what piles do</li>
                <li>Cleaned up reveal sudo / mysql passwords</li>
                <li>Creating a server from the severs page now makes you confirm with warning</li>
                <li>Roll backs now just release the old release without any other commands running , unless the old
                    release is missing
                </li>
            </ul>

            <ul>
                <h4>PHP</h4>
                <li>Composer is now installed via getcomposer.org/installer</li>
            </ul>

            <h2>Fixed</h2>
            <ul>
                <li>Notifications saying you're not connected with your account</li>
                <li>Fixed sudo password reset</li>
                <li>Fixed issue of failed deployment emails not respecting breaking lines</li>
                <li>Fixed Service restart groups</li>
                <li>You can now delete a site before fully configured</li>
                <li>Auto removal of packages after updating the system</li>
                <li>Sites now show the attached servers when making server changes</li>
                <li>Server features now will block feature installs while there is another install running</li>
                <li>Force install of UFW, if for some reason its not installed already</li>
            </ul>

            <ul>
                <h4>PHP</h4>
                <li>The composer update cron job is not attached to the server</li>
            </ul>

            <ul>
                <h4>PHP - Laravel</h4>
                <li>Changed the default order of clearing caches for deployments</li>
            </ul>

            <p>There were a lot of other fixes and performance enhancements, but these are the most notable!</p>
        </div>
    </section>
@endsection

@push('scripts')

@endpush
