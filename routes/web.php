<?php

Route::auth();

Route::get('/', function () {
    if (\Auth::check()) {
        return view('codepier', [
            'user' => \Auth::user()->load(['teams', 'piles.servers'])
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
Route::get('teams/accept/{token}', 'UserTeamController@acceptInvite')->name('teams.accept_invite');

Route::get('/provider/{provider}/link', 'Auth\OauthController@newProvider');
Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');

Route::group(['prefix' => 'webhook'], function () {
    Route::get('/deploy/{siteHashID}', function ($siteHashID) {
        dispatch(new \App\Jobs\DeploySite(
            App\Models\Site::with('server')->findOrFail(\Hashids::decode($siteHashID)[0])
        ));
    })->name('webhook/deploy');



});

Route::group(['middleware' => 'auth', 'prefix' => 'api'], function() {

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::group(['prefix' => 'me', 'namespace' => 'User'], function() {
        Route::resource('/', 'UserController');

    });

    Route::group(['prefix' => 'my', 'namespace' => 'User'], function() {
        Route::resource('subscription', 'Subscription\UserSubscriptionController');
        Route::post('subscription/invoice/{invoice_id}', 'Subscription\UserSubscriptionController@downloadInvoice'); // TODO - convert this to its own resource

        Route::resource('server/providers', 'Providers\UserServerProviderController');
        Route::resource('repository/providers', 'Providers\UserRepositoryProviderController');
        Route::resource('notification/providers', 'Providers\UserNotificationProviderController');

        Route::resource('ssh-keys', 'Features\UserSshKeyController');
    });

    Route::group(['prefix' => 'my'], function() {
        /*
        |--------------------------------------------------------------------------
        | Teamwork Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::group(['prefix' => 'teams',  'namespace' => 'User\Team'], function() {
            Route::resource('/', 'UserTeamController');
            Route::post('switch/{id?}', 'UserTeamController@switchTeam')->name('teams.switch');
        });

        Route::group(['prefix' => 'team',  'namespace' => 'User\Team'], function() {
            Route::resource('members', 'UserTeamMemberController');
            Route::post('members', 'UserTeamMemberController@invite')->name('teams.members.invite');
            Route::post('members/resend/{invite_id}', 'UserTeamMemberController@resendInvite')->name('teams.members.resend_invite');
        });

        /*
        |--------------------------------------------------------------------------
        | Piles Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::group(['prefix' => 'piles', 'namespace' => 'Pile'], function() {
            Route::resource('/', 'PileController');
        });

        /*
       |--------------------------------------------------------------------------
       | Server Routes
       |--------------------------------------------------------------------------
       |
       */

        Route::group(['prefix' => 'servers', 'namespace' => 'Server'], function() {
            Route::resource('/', 'ServerController');
            Route::post('restore/{server_id}', 'ServerController@restore');
            Route::post('restart-database/{server_id}', 'ServerController@restartDatabases');
            Route::post('disk-space/{server_id}', 'ServerController@getDiskSpace');
            Route::post('save/file/{server_id}', 'ServerController@saveFile');
            Route::post('provider/{server_provider_id}/option-regions', 'ServerController@getServerOptionsAndRegions');
            Route::post('restart-server/{server_id}', 'ServerController@restartServer');
            Route::post('restart-web-services/{server_id}', 'ServerController@restartWebServices');
            Route::post('restart-workers/{server_id}', 'ServerController@restartWorkerServices');

            Route::resource('cron-jobs', 'Features\ServerCronJobController');
            Route::resource('daemons', 'Features\ServerDaemonController');
            Route::resource('firewall', 'Features\ServerFirewallController');
            Route::resource('network', 'Features\ServerNetworkController');
            Route::resource('ssh-keys', 'Features\ServerSshKeyController');
            Route::post('ssh-connection', 'Features\ServerSshKeyController@testSSHConnection');
        });

        /*
        |--------------------------------------------------------------------------
        | Site Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::group(['prefix' => 'sites', 'namespace' => 'Site'], function() {
            Route::resource('/', 'SiteController');
            Route::post('deploy', 'SiteController@deploy');

            Route::resource('ssl', 'Features\SSL\SiteSSLController');
            Route::resource('ssl-existing', 'Features\SSL\SiteSSLExistingController');
            Route::resource('ssl-lets-encrypt', 'Features\SSL\SiteSSLLetsEncryptController');

            Route::resource('workers', 'Features\SiteWorkerController');

            Route::resource('hooks', 'Repository\Features\RepositoryHookController');
            Route::resource('repository', 'Repository\SiteRepositoryController');
        });
    });


});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/{any}', function ($any) {
        return view('codepier', [
            'user' => \Auth::user()->load(['teams', 'piles.servers'])
        ]);
    })->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| Stripe Web Hooks
|--------------------------------------------------------------------------
|
*/

Route::post('stripe/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');