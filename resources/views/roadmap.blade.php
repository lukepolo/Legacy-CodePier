@extends('layouts.public')

@section('content')
    <section id="section--pricingintro">
        <h1>CodePier's Major Features RoadMap!</h1>
        <h3 class="text-center">Here are our goals, and what you can <span style="text-decoration: underline;">hope</span> to expect.</h3>
        <br>
    </section>

    <section id="section--pricing" class="section">
        <div class="section--content">
            <div class="pricing">
                <div class="pricing--item">
                    <div class="pricing--header">
                        <div class="pricing--header-name">1.0.5</div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li>Backups Out of Beta</li>
                            <li>Bitts Marketplace Enhancment</li>
                            <li>Installing existing SSL Certificates to other servers</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <h3>April 17th, 2018</h3>
                    </div>
                </div>
                <div class="pricing--item">
                    <div class="pricing--header">
                        <div class="pricing--header-name">1.0.6</div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li>Teams Beta</li>
                            <li>Copying Sites</li>
                            <li>Uninstalling Features</li>
                            <li>Saveable Server Setups</li>
                            <li>Transfering Ownerships of sites and servers</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <h3>May 8th, 2018</h3>
                    </div>
                </div>
                <div class="pricing--item">
                    <div class="pricing--header">
                        <div class="pricing--header-name">1.0.7</div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li>Teams out of Beta</li>
                            <li>UI/UX Enhancements to every part of the app</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <h3>May 22nd, 2018</h3>
                    </div>
                </div>
                <div class="pricing--item">
                    <div class="pricing--header">
                        <div class="pricing--header-name">1.0.8</div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li>Light Theme</li>
                            <li>Ubuntu 18.04 LTS Support</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <h3>June 12th, 2018</h3>
                    </div>
                </div>
                <div class="pricing--item">
                    <div class="pricing--header">
                        <div class="pricing--header-name">1.0.9</div>
                    </div>
                    <div class="pricing--features">
                        <ul>
                            <li>Ruby Beta</li>
                        </ul>
                    </div>
                    <div class="pricing--footer">
                        <h3>July 31st, 2018</h3>
                    </div>
                </div>
            </div>

            <p class="text-center text-bold">
                We have weekly maintenance releases and other minor features come out during these releases.
            </p>
            <br><br>
            <p class="text-center">
                * This time line is subject to change
            </p>
        </div>
    </section>

@endsection

@push('scripts')

@endpush
