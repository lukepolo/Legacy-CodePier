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

                    @include('auth.forms.login')
                    @include('auth.forms.create')
                    @include('auth.forms.reset')

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
    <script>
        function toggleForm() {
          setHiddenInputs('[type="email"]');
          setHiddenInputs('[name="password"]');
          document.getElementById('login_form').classList.toggle('hide');
          document.getElementById('register_form').classList.toggle('hide');
        }

        function setHiddenInputs(selector) {
          var hiddenInputs = getInputs(selector, true);
          var visibleInput = getInputs(selector)[0];
          for(var i = 0; i < hiddenInputs.length; i++) {
            var hiddenInput = hiddenInputs[i];
            hiddenInput.value = visibleInput.value;
          }
        }

        function getInputs(selector, hidden = false) {
          var foundInputs = document.querySelectorAll(selector);
          
          var inputs = [];
          for(var i = 0; i < foundInputs.length; i++) {
            var input = foundInputs[i];
            if(!hidden && input.offsetParent !== null || hidden && input.offsetParent === null) {
              inputs.push(input);
            }
          }
          return inputs;
        }

        var url = new URL(window.location.href);
        if (url.searchParams.get("showRegisterForm")) {
            toggleForm();
        }

        var toggleForms = document.getElementsByClassName('js-toggle-forms')
        for(var i = 0; i < toggleForms.length; i++) {
            var item = toggleForms[i];
            item.onclick = function() {
              toggleForm();
            };
        }

        var forgotForms = document.getElementsByClassName("js-toggle-forgot")
        for(var i = 0; i < forgotForms.length; i++) {
          var item = forgotForms[i];
          item.onclick = function() {
            setHiddenInputs('[type="email"]');
            document.getElementById('login_form').classList.toggle('hide');
            document.getElementById('forgot_form').classList.toggle('hide');
          };
        }
    </script>
@endpush