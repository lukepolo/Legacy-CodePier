@extends('layouts.public')

@section('content')

    <section class="section">
        <div class="section--content">

            <h1>FAQ<span class="small-caps">s</span></h1>
            <p>
                CodePier is designed to customize your sever based off of your site's requirements. Because of
                this we recommend creating your site first,
                set up it's necessary requirements, and finally create or attach a server.
            </p>

            <hr>

            <h3>Common Questions</h3>

            <ol class="faq-list">
                <li><a href="#started">How do I get started?</a></li>
                <li><a href="#languages">What languages do you support?</a></li>
                <li><a href="#sitePriority">What is "site priority design"?</a></li>
                <li><a href="#serverFirst">Can I still create a server first?</a></li>
                <li><a href="#teams">Do you support teams?</a></li>
                <li><a href="#provisioning">What server types can we provision?</a></li>
                <li><a href="#providers">What server providers do you support?</a></li>
                <li><a href="#different">How is CodePier different than another service?</a></li>
                <li><a href="#theme">Do you have a light theme coming?</a></li>
            </ol>

            <h3>Common Problems</h3>

            <ol class="faq-list">
                <li><a href="#ssl">Why isn't my SSL certificate working?</a></li>
                <li><a href="#ssl">Where can I get help?</a></li>
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
                    Typically, you create a server first, then add a site to it. CodePier suggests you set up your site first.
                    Reversing this process lets us determine a recommended configuration for how to set up your servers,
                    saving you time in the setup process.
                </p>

                <h2 id="serverFirst">Can I still create a server first?</h2>
                <p>
                    Yes. Through the "<i class="fa fa-gear"></i>" icon, you can still create a server first, However, setting up the
                    server requirements are likely to take more time than the "site priority" approach.
                </p>

                <h2 id="teams">Do you support teams?</h2>
                <p>
                    Not yet! However it is in our pipeline to have this available in early Q2.
                </p>

                <h2 id="provisioning">What server types can we provision?</h2>
                <p>Currently there are 5 different types of servers:</p>
                <ul>
                    <li>Full Stack <small>- Includes web / database / workers services in one server</small></li>
                    <li>Web <small>- Only web services are installed</small></li>
                    <li>Database <small>- Only database services are installed</small></li>
                    <li>Worker <small>- Only worker services are installed</small></li>
                    <li>Load Balancer <small>- Balances traffic between multiple web server</small></li>
                </ul>

                <h2 id="providers">What server providers do you support?</h2>
                <p>
                    We currently support Digital Ocean, Linode, Vultr, and any custom Ubuntu 16.04 server on any provider.
                </p>

                <h2 id="different">How is CodePier different than another service?</h2>
                <p>
                    We provide extra features out of the box like zero downtime deployments, easy horizontal scaling,
                    <a href="#sitePriority">site priority design</a>, server monitoring
                    and our innovative Events bar allowing you to see real time server commands and statuses.
                    You still have control as everything is hosted on YOUR own servers.
                    We provide all the features necessary to allow you to ship your site from A-Z and make
                    sure your site is running smooth.
                </p>

                <h2 id="theme">Do you have a light theme coming?</h2>
                <p>
                    Yes! It is on our roadmap to include a light theme after some other core features are added..
                </p>
                <h2 id="ssl">Why isn't my SSL certificate working?</h2>
                <p>
                    You need to make sure your A and/or AAAA records are pointing to the correct IP address.
                    Just send us a message in the chat and we can help with that.
                </p>
            </div>

            <br><br><br>
            <hr>

            <h2>Where can I get help?</h2>
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
