@extends('layouts.public')

@section('content')
    <section id="section--pricingintro" class="cover">
        <div class="section--content">
            <h1>Choose a plan that's right for you.</h1>
        </div>
    </section>

    <section id="section--pricing" class="section">
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
                    </ul>
                </div>
                <div class="pricing--footer">
                    <a class="btn btn-primary btn-large">Select</a>
                </div>
            </div>
            <div class="pricing--item">
                <div class="pricing--header">
                    <div class="pricing--header-name">First Mate</div>
                    <div class="pricing--header-price">$29<small>/ month</small></div>
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
                    <a class="btn btn-primary btn-large">Start Trial</a>
                </div>
            </div>
            <div class="pricing--item">
                <div class="pricing--header">
                    <div class="pricing--header-name">Captain</div>
                    <div class="pricing--header-price">$49<small>/ month</small></div>
                </div>
                <div class="pricing--features">
                    <ul>
                        <li><strong>Unlimited</strong> Sites</li>
                        <li><strong>Unlimited</strong> Servers</li>
                        <li>Multiple Server Types</li>
                        <li>Buoys</li>
                        <li>Server Monitoring</li>
                        <li>API Access <small>(Coming Soon!)</small></li>
                        <li>Teams <small>(Coming Soon!)</small></li>
                    </ul>
                </div>
                <div class="pricing--footer">
                    <a class="btn btn-primary btn-large">Start Trial</a>
                </div>
            </div>
        </div>
        <h3 class="text-center">Each paid plan includes a 5 day free trial</h3>
    </section>
@endsection

@push('scripts')

@endpush
