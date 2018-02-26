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
                            <li>1 Site</li>
                            <li>1 Full Stack Server</li>
                            <li><strong>Unlimited</strong> Deployments</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <a href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true"
                           class="btn btn-primary btn-large">Get Started</a>
                    </div>
                </div>
                <div class="pricing--item selected">
                    <div class="pricing--banner">Early Bird Special!</div>
                    <div class="pricing--header">
                        <div class="pricing--header-name">First Mate</div>
                        <div class="pricing--header-price">$19
                            <small>/ month</small>
                        </div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li><strong>Unlimited</strong> Sites</li>
                            <li>30 Servers</li>
                            <li>Multiple Server Types</li>
                            <li>Buoys</li>
                            <li>Server Monitoring</li>
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
                            <li><strong>Unlimited</strong> Sites</li>
                            <li><strong>Unlimited</strong> Servers</li>
                            <li>Multiple Server Types</li>
                            <li>Buoys</li>
                            <li>Server Monitoring</li>
                            <li>Teams</li>
                            <li>API Access</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <div class="pricing--coming"><h3>Coming Soon!</h3></div>
                    </div>
                </div>
            </div>


        </div>
    </section>


    <section id="section--pricingouttro">
    </section>
@endsection

@push('scripts')

@endpush
