<div id="register_form" class="hide">
    <div class="flyform--heading">
        <h2>Create Account</h2>
        <p>Fill out the following fields to create your account.</p>
    </div>

    <form method="POST" action="{{ url('/register') }}">

        {{ csrf_field() }}

        <div class="flyform--group">
            <input type="text" name="name" placeholder="&nbsp;" required>
            <label for="name"><span class="flyform--group-float">Name</span></label>
        </div>
        <div class="flyform--group">
            <input type="email" name="email" placeholder="&nbsp;" required>
            <label for="email"><span class="flyform--group-float">Email</span></label>
        </div>
        <div class="flyform--group">
            <input type="password" name="password" placeholder="&nbsp;" required>
            <label for="password"><span class="flyform--group-float">Password</span></label>
        </div>
        <div class="flyform--group">
            <input type="password" name="password_confirmation" placeholder="&nbsp;" required>
            <label for="password_confirmation"><span class="flyform--group-float">Confirm Password</span></label>
        </div>

        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <button class="btn js-toggle-forms">Cancel</button>
                <button class="btn btn-primary" type="submit">Sign Up</button>
            </div>

        </div>
    </form>
</div>