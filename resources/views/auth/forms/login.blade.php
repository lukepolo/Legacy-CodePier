<div id="login_form">
    <div class="flyform--heading">
        <h2>Login</h2>
    </div>

    <form method="POST">

        @include('auth.errors')

        {{ csrf_field() }}

        <div class="flyform--content">
            <div class="flyform--group">
                <input type="email" name="email" placeholder="&nbsp;" required tabindex="1">
                <label for="email">Email</label>
            </div>

            <div class="flyform--group">
                <input type="password" name="password" placeholder="&nbsp;" required tabindex="2">
                <label for="password">Password</label>
            </div>
        </div>

        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <span class="btn js-toggle-forms" tabindex="4">Create Account</span>
                <button class="btn btn-primary" tabindex="3">Login</button>
            </div>
            <div class="flyform--footer-links">
                <a class="js-toggle-forgot">Forgot password?</a>
            </div>
        </div>
    </form>
</div>