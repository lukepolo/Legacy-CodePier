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
            'index',
        ],
    ]);

    Route::resource('user', 'User\UserController', [
        'except' => 'index', // VERIFIED
    ]);

    Route::group(['prefix' => 'my'], function () {
        Route::group(['namespace' => 'User'], function () {
            Route::resource('subscription/invoices', 'Subscription\UserSubscriptionInvoiceController'); // VERIFIED
            Route::resource('subscription', 'Subscription\UserSubscriptionController'); // VERIFIED
            Route::resource('subscription/invoice/next',
                'Subscription\UserSubscriptionUpcomingInvoiceController'); // VERIFIED

            Route::resource('ssh-keys', 'UserSshKeyController'); // VERIFIED
            Route::resource('server-providers', 'Providers\UserServerProviderController');  // VERIFIED
            Route::resource('repository-providers', 'Providers\UserRepositoryProviderController');  // VERIFIED
            Route::resource('notification-providers', 'Providers\UserNotificationProviderController');  // VERIFIED
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

        Route::resource('team', 'User\Team\UserTeamController');  // VERIFIED

        Route::group(['prefix' => 'team', 'namespace' => 'User\Team'], function () {
            Route::resource('team.members', 'UserTeamMemberController'); // VERIFIED
            Route::post('switch/{id?}', 'UserTeamController@switchTeam')->name('teams.switch'); // VERIFIED
            Route::post('members', 'UserTeamMemberController@invite')->name('teams.members.invite'); // VERIFIED
            Route::post('members/resend/{invite_id}',
                'UserTeamMemberController@resendInvite')->name('teams.members.resend_invite'); // VERIFIED
        });

        /*
        |--------------------------------------------------------------------------
        | Piles Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::resource('piles', 'Pile\PileController'); // VERIFIED
        Route::resource('pile.sites', 'Pile\PileSitesController'); // VERIFIED

        /*
       |--------------------------------------------------------------------------
       | Server Routes
       |--------------------------------------------------------------------------
       |
       */

        Route::resource('servers', 'Server\ServerController');

        Route::group(['namespace' => 'Server'], function () {
            Route::group(['prefix' => 'server'], function () {
                Route::post('restore/{server}', 'ServerController@restore');
                Route::post('server/file/{server}', 'ServerController@getFile');
                Route::post('server/file/{server}', 'ServerController@saveFile');
                Route::post('disk-space/{server}', 'ServerController@getDiskSpace');
                Route::post('restart-server/{server}', 'ServerController@restartServer'); // VERIFIED
                Route::post('restart-database/{server}', 'ServerController@restartDatabases'); // VERIFIED
                Route::post('restart-workers/{server}', 'ServerController@restartWorkerServices');  // VERIFIED
                Route::post('ssh-connection/{server}', 'ServerSshKeyController@testSSHConnection');
                Route::post('restart-web-services/{server}', 'ServerController@restartWebServices'); // VERIFIED
            });

            Route::resource('servers.features', 'ServerFeatureController'); // VERIFIED
            Route::resource('servers.cron-jobs', 'ServerCronJobController'); // VERIFIED
            Route::resource('servers.daemons', 'ServerDaemonController'); // VERIFIED
            Route::resource('servers.firewall', 'ServerFirewallController'); // VERIFIED
            Route::resource('servers.network', 'ServerNetworkController');
            Route::resource('servers.ssh-keys', 'ServerSshKeyController'); // VERIFIED

            Route::resource('servers.sites', 'ServerSiteController'); // VERIFIED
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

    Route::get('server/features', 'Server\ServerFeatureController@getServerFeatures'); // VERIFIED
    Route::get('server/languages', 'Server\ServerFeatureController@getLanguages'); // VERIFIED
    Route::get('server/frameworks', 'Server\ServerFeatureController@getFrameworks'); // VERIFIED

    Route::group(['prefix' => 'auth'], function () {
        Route::group(['prefix' => 'providers', 'namespace' => 'Auth\Providers'], function () {
            Route::resource('server-providers', 'ServerProvidersController'); // VERIFIED
            Route::resource('repository-providers', 'RepositoryProvidersController'); // VERIFIED
            Route::resource('notification-providers', 'NotificationProvidersController'); // VERIFIED
        });
    });

    Route::group(['prefix' => 'server/providers'], function () {
        Route::group([
            'prefix' => \App\Http\Controllers\Auth\OauthController::DIGITAL_OCEAN,
            'namespace' => 'Server\Providers\DigitalOcean',
        ], function () {
            Route::resource('options', 'DigitalOceanServerOptionsController'); // VERIFIED
            Route::resource('regions', 'DigitalOceanServerRegionsController'); // VERIFIED
            Route::resource('features', 'DigitalOceanServerFeaturesController'); // VERIFIED
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
Route::resource('subscription/plans', 'SubscriptionController'); // VERIFIED

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
            'user' => \Auth::user()->load(['teams', 'piles.servers']),
        ]);
    }

    return view('landing');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/{any}', function ($any) {
        return view('codepier', [
            'user' => \Auth::user()->load(['teams', 'piles.servers']),
        ]);
    })->where('any', '.*');
});
