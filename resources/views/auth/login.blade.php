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

                    <div class="jcf-form-wrap" id="login_form">
                        <div class="heading">
                            <h2>Login</h2>
                        </div>

                        <form method="POST" class="validation-form floating-labels">

                            @include('auth.errors')

                            {{ csrf_field() }}
                            <div class="jcf-input-group">
                                <input type="email" name="email" required>
                                <label for="email"><span class="float-label">Email</span></label>
                            </div>
                            <div class="jcf-input-group">
                                <input type="password" name="password" required>
                                <label for="password"><span class="float-label">Password</span></label>
                            </div>
                            <div class="btn-footer">
                                <span class="btn toggle-forms">Create Account</span>
                                <button class="btn btn-primary" type="submit">Login</button>
                                <div class="btn-forgot-pw">
                                    <a class="toggle-forgot">Forgot password?</a>
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