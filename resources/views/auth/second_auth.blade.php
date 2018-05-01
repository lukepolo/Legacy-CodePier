@extends('layouts.app')

@section('content')
    <section class="view">
        <section id="middle" class="section-column full-form">
            <div class="section-content">
                <div class="login-wrap">
                    <div class="img-wrap">
                        <router-link to="/">
                            <img src="/assets/img/CP_Logo-onGray.svg" alt="CodePier" style="display: block;">
                        </router-link>
                    </div>
                    <div id="login_form">
                        <div class="flyform--heading">
                            <h2>Two-factor Authentication (2FA)</h2>
                        </div>

                        <form method="POST" action="{{ action('Auth\SecondAuthController@store') }}">

                            {{ csrf_field() }}

                            <div class="flyform--content">
                                @include('auth.errors')

                                <p>Second auth is enabled for this account. Please enter your token below to log into your account.</p>
                            </div>

                            <div class="flyform--content">
                                <div class="flyform--group">
                                    <input type="password" name="token" placeholder="&nbsp;" required tabindex="1">
                                    <label for="token">Token</label>
                                </div>
                            </div>

                            <div class="flyform--footer">
                                <div class="flyform--footer-btns">
                                    <button class="btn" type="submit" form="cancelForm">Logout</button>
                                    <button class="btn btn-primary" type="submit">Login</button>
                                </div>
                                <div class="flyform--footer-links">
                                    Can't access your token? <a href="mailto:support@codepier.io?subject=[2nd Auth Support]">Email our support team.</a>
                                </div>
                            </div>
                        </form>
                        <form id="cancelForm" method="POST" action="/logout">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection