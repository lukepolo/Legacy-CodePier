<?php

Route::auth();

// TODO - make this a post
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/', function() {
    if(\Auth::check()) {
        return view('codepier', [
            'user' => \Auth::user()
        ]);
    }
    return view('landing');
});

/*
|--------------------------------------------------------------------------
| OAuth Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/provider/{provider}/link', 'Auth\OauthController@newProvider');
Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');

Route::group(['prefix' => 'webhook'], function() {
    Route::get('/diskspace', function() {

        $server = \App\Models\Server::findOrFail(\Hashids::decode(\Request::get('key')));

        dd($server);
    });

    Route::get('/deploy/{siteHashID}', function($siteHashID) {
        dispatch(new \App\Jobs\DeploySite(
            App\Models\Site::with('server')->findOrFail(\Hashids::decode($siteHashID)[0])
        ));
    })->name('webhook/deploy');

});

Route::group(['middleware' => 'auth'], function () {

});

/*
|--------------------------------------------------------------------------
| Stripe Webhooks
|--------------------------------------------------------------------------
|
*/

Route::post('stripe/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');