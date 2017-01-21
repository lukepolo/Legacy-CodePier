<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('auth_code');

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

Route::group(['prefix' => 'webhook'], function () {
    Route::any('/deploy/{siteHashID}', 'WebHookController@deploy');
    Route::get('/loads/{serverHashID}', 'WebHookController@loadMonitor');
    Route::get('/memory/{serverHashID}', 'WebHookController@memoryMonitor');
    Route::get('/diskusage/{serverHashID}', 'WebHookController@diskUsageMonitor');
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
| Catch All Route
|--------------------------------------------------------------------------
|
*/

Route::group(['middleware' => 'auth'], function () {
    Route::get('subscription/invoice/{invoice}', 'User\Subscription\UserSubscriptionInvoiceController@show');
    Route::get('/{any}', 'Controller@app')->where('any', '.*');
});
