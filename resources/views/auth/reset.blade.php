<div id="forgot_form" class="jcf-form-wrap hide">
    <div class="heading">
        <h2>Forgot Password</h2>
    </div>

    <form method="POST" action="{{ url('/password/email') }}" class="validation-form floating-labels">
        {{ csrf_field() }}
        <div class="jcf-input-group">
            <input type="email" name="email" required>
            <label for="email"><span class="float-label">Email</span></label>
        </div>
        <div class="btn-footer">
            <span class="btn toggle-forgot">Cancel</span>
            <button class="btn btn-primary" type="submit">Request Password Reset</button>
        </div>
    </form>
</div>