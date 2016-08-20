@extends('layouts.app')

@section('content')
        <div>
            <div class="jcf-form-wrap">
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


        <h5 class="text-center"> - Or sign in using -</h5>
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
@endsection
