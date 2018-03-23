@extends('layouts.public')

@section('content')

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
