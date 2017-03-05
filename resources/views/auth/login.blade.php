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

                    <p class="text-success">
                        <div class="text-center">
                            <h2>
                                Registration code accepted
                            </h2>
                        </div>
                    </p>

                    <div class="jcf-form-wrap" id="login_form">
                        <div class="heading">
                            <h2>Login</h2>
                        </div>

                        <form method="POST" class="validation-form floating-labels">
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
                                <input type="email" name="email" required>
                                <label for="email"><span class="float-label">Email</span></label>
                            </div>
                            <div class="jcf-input-group">
                                <input type="password" name="password" required>
                                <label for="password"><span class="float-label">Password</span></label>
                            </div>
                            <div class="btn-footer">
                                @if(env('APP_REGISTRATION') || session('auth_code'))
                                    <span class="btn toggle-forms">Create Account</span>
                                @endif
                                <button class="btn btn-primary" type="submit">Login</button>
                            </div>
                        </form>
                    </div><!-- end form-wrap -->

                    <div id="register_form" class="jcf-form-wrap hide">
                        <div class="heading">
                            <h2>Create Account</h2>
                            <p>Fill out the following fields to create your account.</p>
                        </div>

                        <form method="POST" action="{{ url('/register') }}" class="validation-form floating-labels">
                            {{ csrf_field() }}
                            <div class="jcf-input-group">
                                <input type="text" name="name" required>
                                <label for="name"><span class="float-label">Name</span></label>
                            </div>
                            <div class="jcf-input-group">
                                <input type="email" name="email" required>
                                <label for="email"><span class="float-label">Email</span></label>
                            </div>
                            <div class="jcf-input-group">
                                <input type="password" name="password" required>
                                <label for="password"><span class="float-label">Password</span></label>
                            </div>
                            <div class="jcf-input-group">
                                <input type="password" name="password_confirmation" required>
                                <label for="password_confirmation"><span class="float-label">Confirm Password</span></label>
                            </div>
                            <div class="btn-footer">
                                <span class="btn toggle-forms">Cancel</span>
                                <button class="btn btn-primary" type="submit">Sign Up</button>
                            </div>
                        </form>
                    </div><!-- end form-wrap -->

                    <h5 class="text-center"> - Or sign in using -</h5>

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
    <script>
        $(document).on('click', '.toggle-forms', function() {

            $('input[type="email"]:hidden').val($('input[type="email"]:visible').val())
            $('input[type="password"]:hidden').val($('input[type="password"]:visible').val())

            $('#register_form, #login_form').toggleClass('hide')
        })
    </script>
@endpush