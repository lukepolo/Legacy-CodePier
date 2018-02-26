@extends('layouts.public')

@section('content')

    <section class="section">
        <div class="section--content">

            <h1>FAQ<span class="small-caps">s</span></h1>
            <p>
                CodePier is designed to customize your sever based off of your application's requirements. Because of
                this we recommend creating your site first,
                set up it's necessary requirements, and finally create or attach a server.
            </p>

            <hr>

            <h3>Common Questions</h3>

            <ol class="faq-list">
                <li><a href="#started">How do I get started?</a></li>
                <li><a href="#languages">What languages do you support?</a></li>
                <li><a href="#sitePriority">What is "site priority design"?</a></li>
                <li><a href="#teams">Do you support teams?</a></li>
                <li><a href="#provisioning">What server types can we provision?</a></li>
                <li><a href="#providers">What server providers do you support?</a></li>
                <li><a href="#different">How is this different than 'x' company?</a></li>
                <li><a href="#choose">Why should we choose you?</a></li>
                <li><a href="#theme">Do you have a light theme coming?</a></li>
            </ol>

            <h3>Common Problems</h3>

            <ol class="faq-list">
                <li><a href="#ssl">Why isn't my SSL certificate working?</a></li>
            </ol>

            <hr>


            <div class="faq-container">

                <h2 id="started">How do I get started?</h2>
                <p>
                    Here are the basic steps to get you going:
                </p>
                <ol>
                    <li>Create a new site in a pile</li>
                    <li>Enter your repository details</li>
                    <li>Follow the setup wizard to help you fill in your site's requirements</li>
                    <li>Select the server type that you require</li>
                    <li>Deploy!</li>
                </ol>

                <h2 id="languages">What languages do you support?</h2>

                <p>
                    We currently support PHP, HTML, Node, and Single Page Applications.
                </p>

                <h2 id="sitePriority">What is "site priority design"?</h2>

                <p>
                    Usually you create a server, then stick a site on it. Well with CodePier we reserve this , and
                    let the application dictate how your servers are built.
                </p>

                <h2 id="teams">Do you support teams?</h2>
                <p>
                    Not yet! We are working really hard to get the rest of these cool features in before we
                    tackle the teams. We hope to have this out in early Q2.
                </p>

                <h2 id="provisioning">What server types can we provision?</h2>
                <p>Currently there are 5 different types :</p>
                <ul>
                    <li>Full Stack - Includes web / database / workers services in one server</li>
                    <li>Web - Only web services are installed</li>
                    <li>Database - Only database services are installed</li>
                    <li>Worker - Only worker services are installed</li>
                    <li>Load Balancer - Balances traffic between multiple web server</li>
                </ul>

                <h2 id="providers">What server providers do you support?</h2>
                <p>
                    We support Digtal Ocean, Linode, Vultr, and any custom Ubuntu 16.04 server on any provider, heck it can be
                    in your basement!
                </p>

                <h2 id="different">How is this different than 'x' company?</h2>
                <p>
                    We provide a ton of features out of the box, that is all hosted on YOUR servers. We provide
                    features that allow you to ship your application from A-Z with a ton of extra features to make
                    sure your application is running smooth.
                </p>

                <h2 id="choose">Why should we choose you?</h2>
                <p>
                    Spend more than an hour each month on server infastucture , provisiong, deploying etc ? Just think, 1 hour of your time is worth way more than
                    $19 a month! If you decide to cancel, they are your servers, everything is still up and working, you just lose a lot of the core
                    features of CodePier.
                </p>

                <h2 id="theme">Do you have a light theme coming?</h2>
                <p>
                    Yes! Once we finish our core features that we think are needed to provide you with the best
                    expereince, we will make it happen!
                </p>
                <h2 id="ssl">Why isn't my SSL certificate working?</h2>
                <p>
                    You need to make sure your A, AAAA records are pointing to the correct IP address. Just chat with us and we can help with that.
                </p>
            </div>

            <br><br><br>
            <hr>

            <h2>Getting Additional Help</h2>
            <p>
                Click on the "<i class="fa fa-gear"></i>" icon, and select "Get Help", this will connect you with a
                CodePier support <strong>developer</strong>. That's right, a developer.
            </p>
            <br><br>
            <h3>Now get out there and start deploying!</h3>
        </div>
    </section>
@endsection

@push('scripts')

@endpush
