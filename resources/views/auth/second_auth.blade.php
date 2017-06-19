@extends('layouts.app')

@section('content')
    <section class="view">
        <section id="middle" class="section-column">
            <div class="section-content">
                <div class="login-wrap">
                    <div class="jcf-form-wrap" id="login_form">
                        <div class="heading">
                            <h2>Login</h2>
                        </div>

                        <form method="POST" class="validation-form floating-labels" action="{{ action('Auth\SecondAuthController@store') }}">

                            @include('auth.errors')

                            {{ csrf_field() }}

                            <div class="jcf-input-group">
                                <input type="password" name="token" required>
                                <label for="token"><span class="float-label">Token</span></label>
                            </div>

                            <div class="btn-footer">
                                <button class="btn btn-primary" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection