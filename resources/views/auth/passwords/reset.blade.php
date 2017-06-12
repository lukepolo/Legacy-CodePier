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
                            <h2>Password Reset</h2>
                        </div>
                        <form method="POST" action="{{ url('/password/reset') }}" class="validation-form floating-labels">

                            @include('auth.errors')
                            <input type="hidden" name="token" value="{{ $token }}">
                            {{ csrf_field() }}
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
                                <button class="btn btn-primary" type="submit">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
