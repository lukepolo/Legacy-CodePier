@extends('layouts.public')

@section('content')
    <p>
        CodePier is designed to customize your sever based off of your application's requirements. Because of this we recommend creating your site first,
        set up it's necessary requirements, and finally create or attach a server.
    </p>

    <h3>Getting Started</h3>
    <div>
        <p>Here are the basic steps to get you going:</p>
        <ol>
            <li>Create a new site in a pile</li>
            <li>Enter your repository details</li>
            <li>Follow the setup wizard to help you fill in your site's requirements</li>
            <li>Select the server type that you require</li>
            <li>Deploy!</li>
        </ol>
    </div>

    <h3>Getting Help</h3>
    <p>
        Click on the "<i class="fa fa-gear"></i>" icon, and select "Get Help", this will connect you with a CodePier support <strong>developer</strong>. That's right, a developer.
    </p>

    <p>Now get out there and start deploying!</p>


    <ol>
        <li>1. What languages do you support?</li>
        <li>2. Do you have a light theme coming?</li>
        <li>2. What is "site priority design"?</li>
        <li>3. Do you support teams?</li>
        <li>4. What server types can we provision?</li>
        <li>5. What server providers do you support?</li>
        <li>x. How is this different than "X" company?</li>
        <li>x. Why should we choose you?</li>
        <li></li>
    </ol>

    <h1>Common Problems</h1>
    <ol>
        <li>Why isn't my SSL certificate working?</li>
        <li></li>
        <li></li>
        <li></li>
    </ol>
    <p id="languages">
        We currently support PHP, HTML, Node, and single Page applications. We are working on getting more languages integrated such as Ruby, GO, and Python.
    </p>
    <p id="site-first">

    </p>
@endsection

@push('scripts')

@endpush
