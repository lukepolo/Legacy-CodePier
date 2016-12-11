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

Route::auth();
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('auth_code');

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
    Route::get('/deploy/{siteHashID}', 'WebHookController@deploy');
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
