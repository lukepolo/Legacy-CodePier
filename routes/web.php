<?php

// Preview Emails
use Illuminate\Mail\Markdown;

if (config('app.env') === 'local') {
    Route::get('welcomeEmail', function () {
        $markdown = new Markdown(view(), config('mail.markdown'));

        return $markdown->render('mail.welcome', [
            'user' => \Auth::user(),
        ]);
    });
}

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
*/
Route::group([
    'middleware' => 'role:admin',
], function () {
    Route::get('/change-user/{userId}', 'ChangeUserController@store');
});

Route::get('/admin/cancel', 'ChangeUserController@destroy');

/*
|--------------------------------------------------------------------------
| OAuth Routes
|--------------------------------------------------------------------------
|
*/
Route::get('/provider/{provider}/link', 'Auth\OauthController@newProvider');
Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');

/*
|--------------------------------------------------------------------------
| Stripe Web Hooks
|--------------------------------------------------------------------------
|
*/

Route::post('stripe/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');

/*
|--------------------------------------------------------------------------
| Resource Routes
|--------------------------------------------------------------------------
|
*/
Route::resource('subscription/plans', 'SubscriptionController');

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
*/

// TODO - put into microservice
Route::group([
    'prefix' => 'webhook',
    'domain' => config('app.url_stats'),
], function () {
    Route::get('/loads/{serverHashId}', 'WebHookController@loadMonitor');
    Route::get('/memory/{serverHashId}', 'WebHookController@memoryMonitor');
    Route::get('/diskusage/{serverHashId}', 'WebHookController@diskUsageMonitor');
    Route::get('/{any}', 'Controller@redirectToApp')->where('any', '.*');
});

Route::group([
    'prefix' => 'webhook',
], function () {
    Route::any('/deploy/{siteHashId}', 'WebHookController@deploy');
    Route::any('/server/{serverHashId}/ssl/updated', 'WebHookController@serverSslCertificateUpdated');
    Route::get('/{any}', 'Controller@redirectToApp')->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| LifeLine Routes
|--------------------------------------------------------------------------
|
*/

Route::group([
    'domain' => config('app.url_lifelines'),
], function () {
    Route::get('{lifelineHashId}', 'LifeLineController@update');
    Route::get('/{any}', 'Controller@redirectToApp')->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| Accept Team Request Route
|--------------------------------------------------------------------------
|
*/
Route::get('teams/accept/{token}', 'User\Team\UserTeamController@acceptInvite')->name('teams.accept_invite');

/*
|--------------------------------------------------------------------------
| Style Guide Routes
|--------------------------------------------------------------------------
|
*/

Route::group([
    'domain' => 'style-guide.codepier.dev',
], function () {
    Route::get('/', 'PublicController@styleGuide');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/pricing', 'PricingController@index');
Route::get('/privacy', 'PublicController@privacy');
Route::post('/subscribe', 'PublicController@subscribe');
Route::get('/terms-of-service', 'PublicController@termsOfService');

/*
|--------------------------------------------------------------------------
| App Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/events-bar', 'Controller@appEventsBar');
Route::get('/', 'Controller@app');

Route::group([
    'middleware' => [
        'auth',
    ],
], function () {
    Route::get('second-auth', 'Auth\SecondAuthController@show');
    Route::post('second-auth', 'Auth\SecondAuthController@store');

    Route::post('user/resend-confirmation', 'User\UserConfirmController@store');
    Route::get('user/{code}/confirm-registration', 'User\UserConfirmController@update');
});

Route::group([
    'middleware' => [
        'auth',
        'second_auth',
    ],
], function () {
    Route::get('subscription/invoices/{invoice}', 'User\Subscription\UserSubscriptionInvoiceController@show');
    Route::get('/{any}', 'Controller@app')->where('any', '.*');
});
