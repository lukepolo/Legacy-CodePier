@component('mail::message')
# Thanks for joining CodePier!

@component('vendor.mail.html.cover')
    Hi {{ $user->name }}, we are excited to get you up and running but we need to verify your email address to continue.
    @component('mail::button', ['url' => action('User\UserConfirmController@update', $user->encode())])
        Confirm Email
    @endcomponent
@endcomponent

To get started you should check our "how to" guides <a href="#">here</a>.

@endcomponent
