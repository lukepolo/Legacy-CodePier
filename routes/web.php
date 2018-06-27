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

Route::get('test', function () {
    dispatch_now(new \App\Jobs\UserDataBundle(\Auth::user()));
});
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
Route::domain(config('app.url_stats'))->prefix('webhook')->group(function () {
    Route::get('/loads/{serverHashId}', 'WebHookController@loadMonitor');
    Route::get('/memory/{serverHashId}', 'WebHookController@memoryMonitor');
    Route::get('/diskusage/{serverHashId}', 'WebHookController@diskUsageMonitor');
    Route::any('/server/{serverHashId}/ssl/updated', 'WebHookController@serverSslCertificateUpdated');
    Route::get('/{any}', 'Controller@redirectToApp')->where('any', '.*');
});

// TODO - we need to prefix these - kinda hard now since we have things deployed
Route::prefix('webhook')->group(function () {
    Route::any('/deploy/{siteHashId}', 'WebHookController@deploy');
    Route::any('/schema-backups/{serverHashId}', 'WebHookController@databaseBackups');
    Route::get('/{any}', 'Controller@redirectToApp')->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| LifeLine Routes
|--------------------------------------------------------------------------
|
*/

Route::domain(config('app.url_lifelines'))->group(function () {
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

Route::domain('style-guide.codepier.dev')->group(function () {
    Route::get('/', 'PublicController@styleGuide');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/faq', 'PublicController@faq');
Route::get('/pricing', 'PricingController@index');
Route::get('/privacy', 'PublicController@privacy');
Route::get('/change-log', 'PublicController@changeLog');
Route::get('/all-features', 'PublicController@allFeatures');
Route::get('/terms-of-service', 'PublicController@termsOfService');

/*
|--------------------------------------------------------------------------
| App Routes
|--------------------------------------------------------------------------
|
*/
Route::domain(config('app.url'))->group(function () {

    // Authentication / Register Routes...
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

    // Password Reset Routes...
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/change-user/{userId}', 'ChangeUserController@store');
    });

    Route::get('/admin/cancel', 'ChangeUserController@destroy');

    Route::middleware([
        'auth',
    ])->group(function () {
        Route::get('second-auth', 'Auth\SecondAuthController@show');
        Route::post('second-auth', 'Auth\SecondAuthController@store');

        Route::post('user/resend-confirmation', 'User\UserConfirmController@store');
        Route::get('user/{code}/confirm-registration', 'User\UserConfirmController@update');
    });

    Route::middleware([
        'auth',
        'second_auth',
    ])->group(function () {
        Route::get('/roadmap', 'PublicController@roadmap');
        Route::get('subscription/invoices/{invoice}', 'User\Subscription\UserSubscriptionInvoiceController@show');
        Route::get('/events-bar', 'Controller@appEventsBar');
        Route::get('/{any}', 'Controller@app')->where('any', '.*');
    });
});

/*
|--------------------------------------------------------------------------
| App Routes
|--------------------------------------------------------------------------
|
*/

Route::redirect('login', action('Auth\LoginController@login'));
Route::get('/', 'Controller@welcome');
