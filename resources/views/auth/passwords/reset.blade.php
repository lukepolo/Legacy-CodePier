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
                            <h2>Password Reset</h2>
                        </div>

                        <form method="POST" action="{{ url('/password/reset') }}" class="validation-form floating-labels">

                            @include('auth.errors')

                            <input type="hidden" name="token" value="{{ $token }}">
                            {{ csrf_field() }}

                            <div class="flyform--content">
                                <div class="flyform--group">
                                    <input type="email" name="email" required tabindex="1">
                                    <label for="email"><span class="float-label">Email</span></label>
                                </div>
                                <div class="flyform--group">
                                    <input type="password" name="password" required tabindex="2">
                                    <label for="password"><span class="float-label">Password</span></label>
                                </div>
                                <div class="flyform--group">
                                    <input type="password" name="password_confirmation" required tabindex="3">
                                    <label for="password_confirmation"><span class="float-label">Confirm Password</span></label>
                                </div>
                            </div>

                            <div class="flyform--footer">
                                <div class="flyform--footer-btns">
                                    <button class="btn btn-primary" type="submit" tabindex="4">Reset Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </section>
    </section>
@endsection