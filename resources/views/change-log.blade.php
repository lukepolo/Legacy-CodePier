@extends('layouts.public')

@section('content')

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
