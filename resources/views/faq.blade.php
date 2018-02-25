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
                    We currently support PHP, HTML, Node, and Single Page Applications. We are working on getting more
                    languages integrated such as Ruby, GO, and Python.
                </p>

                <h2 id="sitePriority">What is "site priority design"?</h2>
                <h2 id="teams">Do you support teams?</h2>
                <h2 id="provisioning">What server types can we provision?</h2>
                <h2 id="providers">What server providers do you support?</h2>
                <h2 id="different">How is this different than 'x' company?</h2>
                <h2 id="choose">Why should we choose you?</h2>
                <h2 id="theme">Do you have a light theme coming?</h2>
                <h2 id="ssl">Why isn't my SSL certificate working?</h2>
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
