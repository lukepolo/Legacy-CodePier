@extends('layouts.public')

@section('content')
    <section id="section--pricingintro">
        <h1>Choose a plan that's right for you.</h1>
        <h3 class="text-center">Each paid plan includes a 5 day free trial</h3>
        <br>
    </section>

    <section id="section--pricing" class="section">
        <div class="section--content">
            <div class="pricing">
                <div class="pricing--item">
                    <div class="pricing--header">
                        <div class="pricing--header-name">Riggers</div>
                        <div class="pricing--header-price">FREE</div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li><strong>Unlimited</strong> Deployments</li>
                            <li>1 Site</li>
                            <li>1 Full Stack Server</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <a href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true"
                           class="btn btn-primary btn-large">Get Started</a>
                    </div>
                </div>
                <div class="pricing--item selected">
                    <div class="pricing--header">
                        <div class="pricing--header-name">First Mate</div>
                        <div class="pricing--header-price">$19
                            <small>/ month</small>
                        </div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li><strong>Unlimited</strong> Sites</li>
                            <li>Everything in Riggers</li>
                            <li>30 Servers</li>
                            <li>Multiple Server Types</li>
                            <li>Server Monitoring</li>
                            <li>15GB of Database Backups <small>In BETA!</small></li>
                            <li>Bitts - Custom Scripts</li>
                            <li>Buoys - 1 Click Installs</li>
                            <li>Check for Invalid / Expired SSL Certificate Daily</li>
                            {{--<li>1 Month of Event Retention</li>--}}
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <a href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true"
                           class="btn btn-primary btn-large">Start Trial</a>
                    </div>
                </div>
                <div class="pricing--item">
                    <div class="pricing--header">
                        <div class="pricing--header-name">Captain</div>
                        <div class="pricing--header-price">$49
                            <small>/ month</small>
                        </div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li><strong>Priority Support</strong></li>
                            <li><strong>Unlimited</strong> Servers</li>
                            <li>Everything in First Mate</li>
                            <li>Teams</li>
                            <li>API Access</li>
                            <li>100GB of Database Backups</li>
                            {{--<li>1 Year of Event Retention</li>--}}
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <div class="pricing--coming"><h3>Coming Soon!</h3></div>
                    </div>
                </div>
            </div>

            <p class="text-center">
                <a href="{{ action('PublicController@allFeatures') }}" class="btn btn-large btn-default">View All Features</a>
            </p>
        </div>
    </section>


    <section id="section--pricingouttro">
    </section>
@endsection

@push('scripts')

@endpush
