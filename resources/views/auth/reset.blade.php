<div id="forgot_form" class="hide">
    <div class="flyform--heading">
        <h2>Forgot Password</h2>
    </div>

    <form method="POST" action="{{ url('/password/email') }}" class="validation-form floating-labels">

        {{ csrf_field() }}

        <div class="flyform--group">
            <input type="email" name="email" required>
            <label for="email"><span class="flyform--group-float">Email</span></label>
        </div>
        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <button class="btn js-toggle-forgot">Cancel</button>
                <button class="btn btn-primary" type="submit">Reset Password</button>
            </div>
        </div>
    </form>
</div>