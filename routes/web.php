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

/*
|--------------------------------------------------------------------------
| OAuth Routes
|--------------------------------------------------------------------------
|
*/
Route::get('/provider/{provider}/link', 'Auth\OauthController@newProvider');
Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');

Route::group(['middleware' => 'auth', 'prefix' => 'api'], function () {

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::resource('me', 'User\UserController', [ // VERIFIED
        'only' => [
            'index'
        ]
    ]);

    Route::resource('user', 'User\UserController', [
        'except' => 'index' // VERIFIED
    ]);

    Route::group(['prefix' => 'my'], function () {

        Route::group(['namespace' => 'User'], function () {

            Route::resource('subscription/invoices', 'Subscription\UserSubscriptionInvoiceController'); // VERIFIED
            Route::resource('subscription', 'Subscription\UserSubscriptionController'); // VERIFIED
            Route::resource('subscription/invoice/next', 'Subscription\UserSubscriptionUpcomingInvoiceController'); // VERIFIED

            Route::resource('ssh-keys', 'UserSshKeyController'); // VERIFIED
            Route::resource('server-providers', 'Providers\UserServerProviderController');
            Route::resource('repository-providers', 'Providers\UserRepositoryProviderController');
            Route::resource('notification-providers', 'Providers\UserNotificationProviderController');
        });

        /*
        |--------------------------------------------------------------------------
        | Events Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::resource('events', 'EventController');

        /*
        |--------------------------------------------------------------------------
        | Teamwork Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::resource('team', 'User\Team\UserTeamController');

        Route::group(['prefix' => 'team', 'namespace' => 'User\Team'], function () {
            Route::resource('team.members', 'UserTeamMemberController');
            Route::post('switch/{id?}', 'UserTeamController@switchTeam')->name('teams.switch');
            Route::post('members', 'UserTeamMemberController@invite')->name('teams.members.invite');
            Route::post('members/resend/{invite_id}', 'UserTeamMemberController@resendInvite')->name('teams.members.resend_invite');
        });

        /*
        |--------------------------------------------------------------------------
        | Piles Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::resource('piles', 'Pile\PileController');
        Route::resource('pile.sites', 'Pile\PileSitesController');

        /*
       |--------------------------------------------------------------------------
       | Server Routes
       |--------------------------------------------------------------------------
       |
       */

        Route::resource('servers', 'Server\ServerController');

        Route::group(['namespace' => 'Server'], function () {

            Route::group(['prefix' => 'server'], function () {
                Route::post('restore/{server_id}', 'ServerController@restore');
                Route::get('server/file/{server_id}', 'ServerController@getFile');
                Route::post('server/file/{server_id}', 'ServerController@saveFile');
                Route::post('disk-space/{server_id}', 'ServerController@getDiskSpace');
                Route::post('ssh-connection', 'ServerSshKeyController@testSSHConnection');
                Route::post('restart-server/{server_id}', 'ServerController@restartServer');
                Route::post('restart-database/{server_id}', 'ServerController@restartDatabases');
                Route::post('restart-workers/{server_id}', 'ServerController@restartWorkerServices');
                Route::post('restart-web-services/{server_id}', 'ServerController@restartWebServices');
            });

            Route::resource('servers.cron-jobs', 'ServerCronJobController');
            Route::resource('servers.daemons', 'ServerDaemonController');
            Route::resource('servers.firewall', 'ServerFirewallController');
            Route::resource('servers.network', 'ServerNetworkController');
            Route::resource('servers.ssh-keys', 'ServerSshKeyController');

            Route::resource('servers.sites', 'ServerSiteController');

        });

        /*
        |--------------------------------------------------------------------------
        | Site Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::resource('sites', 'Site\SiteController');

        Route::group(['namespace' => 'Site'], function () {

            Route::group(['prefix' => 'sites'], function () {
                Route::post('deploy', 'SiteController@deploy');
            });

            Route::resource('site.servers', 'SiteServerController');
            Route::resource('site.workers', 'SiteWorkerController');
            Route::resource('site.hooks', 'Repository\RepositoryHookController');
            Route::resource('site.certificate', 'Certificate\SiteSSLController');
            Route::resource('site.repository', 'Repository\SiteRepositoryController');
            Route::resource('site.certificate/existing', 'Certificate\SiteSSLExistingController');
            Route::resource('site.certificate/lets-encrypt/', 'Certificate\SiteSSLLetsEncryptController');
        });

    });

    Route::group(['prefix' => 'auth'], function () {
        Route::group(['prefix' => 'providers', 'namespace' => 'Auth\Providers'], function () {
            Route::resource('server-providers', 'ServerProvidersController');
            Route::resource('repository-providers', 'RepositoryProvidersController');
            Route::resource('notification-providers', 'NotificationProvidersController');
        });
    });

    Route::group(['prefix' => 'server/providers'], function () {
        Route::group([
            'prefix' => \App\Http\Controllers\Auth\OauthController::DIGITAL_OCEAN,
            'namespace' => 'Server\Providers\DigitalOcean'
        ], function () {
            Route::resource('options', 'DigitalOceanServerOptionsController');
            Route::resource('regions', 'DigitalOceanServerRegionsController');
            Route::resource('features', 'DigitalOceanServerFeaturesController');
        });
    });
});

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
    Route::get('/deploy/{siteHashID}', function ($siteHashID) {
        dispatch(new \App\Jobs\DeploySite(
            App\Models\Site::with('server')->findOrFail(\Hashids::decode($siteHashID)[0])
        ));
    })->name('webhook/deploy');
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
| Catch All Routes
|--------------------------------------------------------------------------
|
*/
Route::get('/', function () {
    if (\Auth::check()) {
        return view('codepier', [
            'user' => \Auth::user()->load(['teams', 'piles.servers'])
        ]);
    }
    return view('landing');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/{any}', function ($any) {
        return view('codepier', [
            'user' => \Auth::user()->load(['teams', 'piles.servers'])
        ]);
    })->where('any', '.*');
});
