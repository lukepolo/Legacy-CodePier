@extends('layouts.app')

@section('content')
    <section class="view">
        <section id="middle" class="section-column">
            <div class="section-content">
                <div class="login-wrap">
                    <div class="img-wrap">
                        <router-link to="/">
                            <img src="/assets/img/codepier_w.svg" alt="CodePier" style="display: block;">
                        </router-link>
                    </div>


                    <div id="login_form">
                        <div class="flyform--heading">
                            <h2>Login</h2>
                        </div>

                        <form method="POST">

                            @include('auth.errors')

                            {{ csrf_field() }}

                            <div class="flyform--group">
                                <input type="email" name="email" placeholder="&nbsp;" required tabindex="1">
                                <label for="email"><span class="flyform--group-float">Email</span></label>
                            </div>

                            <div class="flyform--group">
                                <input type="password" name="password" placeholder="&nbsp;" required tabindex="2">
                                <label for="password"><span class="flyform--group-float">Password</span></label>
                            </div>

                            <div class="flyform--footer">
                                <div class="flyform--footer-btns">
                                    <button class="btn js-toggle-forms" tabindex="4">Create Account</button>
                                    <button class="btn btn-primary" tabindex="3">Login</button>
                                </div>
                                <div class="flyform--footer-links">
                                    <a class="js-toggle-forgot">Forgot password?</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @include('auth.create')
                    @include('auth.reset')

                    <h5 class="text-center"> - Or sign in using -</h5>

                    <ul class="list-inline text-center">
                        <li>
                            <a href="{{ action('Auth\OauthController@newProvider', 'github') }}" class="btn btn-primary btn-circle"><i class="fa fa-github"></i></a>
                        </li>
                        <li>
                            <a href="{{ action('Auth\OauthController@newProvider', 'bitbucket') }}" class="btn btn-primary btn-circle"><i class="fa fa-bitbucket"></i></a>
                        </li>
                        <li>
                            <a href="{{ action('Auth\OauthController@newProvider', 'gitlab') }}" class="btn btn-primary btn-circle"><i class="fa fa-gitlab"></i></a>
                        </li>
                    </ul>
                </div>

            </div>
        </section>
    </section>
@endsection

@push('scripts')
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
    <script src="{{ mix('js/public.js') }}"></script>
@endpush