<div id="register_form" class="jcf-form-wrap hide">
    <div class="heading">
        <h2>Create Account</h2>
        <p>Fill out the following fields to create your account.</p>
    </div>

    <form method="POST" action="{{ url('/register') }}" class="validation-form floating-labels">
        {{ csrf_field() }}
        <div class="jcf-input-group">
            <input type="text" name="name" required>
            <label for="name"><span class="float-label">Name</span></label>
        </div>
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
            <span class="btn toggle-forms">Cancel</span>
            <button class="btn btn-primary" type="submit">Sign Up</button>
        </div>
    </form>
</div>