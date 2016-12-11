@extends('layouts.app')

@section('content')
    <section class="view">
        <section id="middle" class="section-column">
            <div class="section-content">
    <div class="login-wrap">
        <div class="img-wrap">
            <router-link to="/">
                <img src="/assets/img/kodi_w.svg" alt="CodePier" style="display: block;">
            </router-link>
        </div>

        @if(empty(session('auth_code')))
            <div class="jcf-form-wrap" id="login-form">
                <div class="heading">
                    <h2>Login</h2>
                </div>

                <form method="post" class="validation-form floating-labels">
                    @if (count($errors) > 0)
                        <p class="text-error">
                            <div class="text-center">
                                <ul style="list-style: none; padding: 0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <br>
                        </p>
                    @endif

                    {{ csrf_field() }}
                    <div class="jcf-input-group">
                        <input type="email" id="email" name="email" required>
                        <label for="email"><span class="float-label">Email</span></label>
                    </div>
                    <div class="jcf-input-group">
                        <input type="password" id="password" name="password" required>
                        <label for="password"><span class="float-label">Password</span></label>
                    </div>
                    <div class="btn-footer">
                        @if(env('APP_REGISTRATION'))
                            {{--<button class="btn">Create Account</button>--}}
                        @endif
                        <button class="btn btn-primary" type="submit">Login</button>
                    </div>
                </form>
            </div><!-- end form-wrap -->
        @else
            <h3 class="text-center">Thanks for joining the Alpha!</h3>
        @endif


        <div class="jcf-form-wrap hide" id="login-form">
            <div class="heading">
                <h2>Create Account</h2>
                <p>Fill out the following fields to create your account.</p>
            </div>

            <form action="#0" method="post" class="validation-form floating-labels">
                <div class="jcf-input-group">
                    <input type="text" id="firstName" name="firstName" required>
                    <label for="firstName"><span class="float-label">First Name</span></label>
                </div>
                <div class="jcf-input-group">
                    <input type="text" id="lastName" name="lastName" required>
                    <label for="lastName"><span class="float-label">Last Name</span></label>
                </div>
                <div class="jcf-input-group">
                    <input type="email" id="email" name="email" required>
                    <label for="email"><span class="float-label">Email</span></label>
                </div>
                <div class="jcf-input-group">
                    <input type="password" id="password" name="password" required>
                    <label for="password"><span class="float-label">Password</span></label>
                </div>
                <div class="btn-footer">
                    <button class="btn">Cancel</button>
                    <button class="btn btn-primary" type="submit">Sign Up</button>
                </div>
            </form>
        </div><!-- end form-wrap -->

        @if(empty(session('auth_code')))
            <h5 class="text-center"> - Or sign in using -</h5>
        @else
            @if (count($errors) > 0)
                <p class="text-error">
                    <div class="text-center">
                        <ul style="list-style: none; padding: 0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <br>
                </p>
            @endif
        @endif
        <ul class="list-inline text-center">
            <li>
                <a href="{{ action('Auth\OauthController@newProvider', 'github') }}" class="btn btn-primary btn-circle"><i class="fa fa-github"></i></a>
            </li>
            <li>
                <a href="{{ action('Auth\OauthController@newProvider', 'bitbucket') }}" class="btn btn-primary btn-circle"><i class="fa fa-bitbucket"></i></a>
            </li>
            <li>
                <a href="{{ action('Auth\OauthController@newProvider', 'digitalocean') }}" class="btn btn-primary btn-circle"><i class="fa fa-server"></i></a>
            </li>
        </ul>
    </div>

            </div>
        </section>
    </section>
@endsection