<div id="register_form" class="hide">
    <div class="flyform--heading">
        <h2>Create Account</h2>
        <p>Fill out the following fields to create your account.</p>
    </div>

    <form method="POST" action="{{ url('/register') }}">

        {{ csrf_field() }}

        <div class="flyform--content">
            <div class="flyform--group">
                <input type="text" name="name" placeholder="&nbsp;" required>
                <label for="name">Name</label>
            </div>
            <div class="flyform--group">
                <input type="email" name="email" placeholder="&nbsp;" required>
                <label for="email">Email</label>
            </div>
            <div class="flyform--group">
                <input type="password" name="password" placeholder="&nbsp;" required>
                <label for="password">Password</label>
            </div>
            <div class="flyform--group">
                <input type="password" name="password_confirmation" placeholder="&nbsp;" required>
                <label for="password_confirmation">Confirm Password</label>
            </div>
        </div>

        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <span class="btn js-toggle-forms">Cancel</span>
                <button class="btn btn-primary" type="submit">Sign Up</button>
            </div>

        </div>
    </form>
</div>