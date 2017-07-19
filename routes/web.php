<?php

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
Route::group(['prefix' => 'webhook'], function () {
    Route::any('/deploy/{siteHashID}', 'WebHookController@deploy');
    Route::get('/loads/{serverHashID}', 'WebHookController@loadMonitor');
    Route::get('/memory/{serverHashID}', 'WebHookController@memoryMonitor');
    Route::get('/diskusage/{serverHashID}', 'WebHookController@diskUsageMonitor');
});

/*
|--------------------------------------------------------------------------
| LifeLine Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['domain' => config('app.url_lifelines')], function () {
    Route::get('{lifelineHashId}', 'LifeLineController@update');
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

Route::group(['domain' => 'style-guide.codepier.dev'], function () {
    Route::get('/', 'PublicController@styleGuide');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/privacy', 'PublicController@privacy');
Route::post('/subscribe', 'PublicController@subscribe');
Route::get('/terms-of-service', 'PublicController@termsOfService');

/*
|--------------------------------------------------------------------------
| App Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', 'Controller@app');

Route::group(['middleware' => ['auth']], function () {
    Route::get('second-auth', 'Auth\SecondAuthController@show');
    Route::post('second-auth', 'Auth\SecondAuthController@store');
});

Route::group(['middleware' => ['auth', 'second_auth']], function () {
    Route::get('slack-invite', 'User\UserController@slackInvite');
    Route::get('subscription/invoice/{invoice}', 'User\Subscription\UserSubscriptionInvoiceController@show');
    Route::get('/{any}', 'Controller@app')->where('any', '.*');
});
