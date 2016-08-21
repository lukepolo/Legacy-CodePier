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

Route::get('/', function () {
    if (\Auth::check()) {
        return view('codepier', [
            'user' => \Auth::user()->load(['teams', 'piles.servers'])
        ]);
    }
    return view('landing');
});

Route::get('/test', function () {
    event(new \App\Events\Server\ServerProvisionStatusChanged(\App\Models\Server::findOrFail(1), 'MORE DONE', '70'));
});

/*
|--------------------------------------------------------------------------
| OAuth Routes
|--------------------------------------------------------------------------
|
*/
Route::get('teams/accept/{token}', 'User\Team\UserTeamController@acceptInvite')->name('teams.accept_invite');

Route::get('/provider/{provider}/link', 'Auth\OauthController@newProvider');
Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');

Route::group(['prefix' => 'webhook'], function () {
    Route::get('/deploy/{siteHashID}', function ($siteHashID) {
        dispatch(new \App\Jobs\DeploySite(
            App\Models\Site::with('server')->findOrFail(\Hashids::decode($siteHashID)[0])
        ));
    })->name('webhook/deploy');
});

Route::group(['middleware' => 'auth', 'prefix' => 'api'], function () {

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::resource('subscription/plans', 'SubscriptionController');

    Route::group(['prefix' => 'me', 'namespace' => 'User'], function () {
        Route::resource('/', 'UserController', [
            'parameters' => [
                '' => 'user'
            ]
        ]);
    });

    Route::group(['prefix' => 'my', 'namespace' => 'User'], function () {
        Route::resource('subscription', 'Subscription\UserSubscriptionController');
        Route::resource('subscription/invoices', 'Subscription\UserSubscriptionInvoiceController');
        Route::resource('subscription/invoice/upcoming', 'Subscription\UserSubscriptionUpcomingInvoiceController');

        Route::resource('server/providers', 'Providers\UserServerProviderController');
        Route::resource('repository/providers', 'Providers\UserRepositoryProviderController');
        Route::resource('notification/providers', 'Providers\UserNotificationProviderController');

        Route::resource('ssh-keys', 'Features\UserSshKeyController');
    });

    Route::group(['prefix' => 'my'], function () {
        /*
        |--------------------------------------------------------------------------
        | Teamwork Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::group(['prefix' => 'teams', 'namespace' => 'User\Team'], function () {
            Route::resource('/', 'UserTeamController', [
                'parameters' => [
                    '' => 'team'
                ]
            ]);
            Route::post('switch/{id?}', 'UserTeamController@switchTeam')->name('teams.switch');
        });

        Route::group(['prefix' => 'team', 'namespace' => 'User\Team'], function () {
            Route::resource('team.members', 'UserTeamMemberController');
            Route::post('members', 'UserTeamMemberController@invite')->name('teams.members.invite');
            Route::post('members/resend/{invite_id}', 'UserTeamMemberController@resendInvite')->name('teams.members.resend_invite');
        });

        /*
        |--------------------------------------------------------------------------
        | Piles Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::group(['prefix' => 'piles', 'namespace' => 'Pile'], function () {
            Route::resource('/', 'PileController', [
                'parameters' => [
                    '' => 'pile'
                ]
            ]);

            Route::resource('sites', 'PileSitesController', [
                'parameters' => [
                    'sites' => 'pile'
                ]
            ]);

        });

        /*
       |--------------------------------------------------------------------------
       | Server Routes
       |--------------------------------------------------------------------------
       |
       */

        Route::group(['prefix' => 'servers', 'namespace' => 'Server'], function () {

            Route::post('restore/{server_id}', 'ServerController@restore');
            Route::post('restart-database/{server_id}', 'ServerController@restartDatabases');
            Route::post('disk-space/{server_id}', 'ServerController@getDiskSpace');
            Route::get('server/file/{server_id}', 'ServerController@getFile');
            Route::post('server/file/{server_id}', 'ServerController@saveFile');
            Route::post('restart-server/{server_id}', 'ServerController@restartServer');
            Route::post('restart-web-services/{server_id}', 'ServerController@restartWebServices');
            Route::post('restart-workers/{server_id}', 'ServerController@restartWorkerServices');

            Route::resource('cron-jobs', 'Features\ServerCronJobController');
            Route::resource('daemons', 'Features\ServerDaemonController');
            Route::resource('firewall', 'Features\ServerFirewallController');
            Route::resource('network', 'Features\ServerNetworkController');
            Route::resource('ssh-keys', 'Features\ServerSshKeyController');
            Route::post('ssh-connection', 'Features\ServerSshKeyController@testSSHConnection');


            Route::resource('/', 'ServerController', [
                'parameters' => [
                    '' => 'server'
                ]
            ]);

        });

        Route::group(['prefix' => 'server', 'namespace' => 'Server'], function () {
            Route::resource('sites', 'ServerSiteController', [
                'parameters' => [
                    'sites' => 'server'
                ]
            ]);
        });
        /*
        |--------------------------------------------------------------------------
        | Site Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::group(['prefix' => 'sites', 'namespace' => 'Site'], function () {

            Route::post('deploy', 'SiteController@deploy');

            Route::resource('servers', 'SiteServerController', [
                'parameters' => [
                    'server' => 'site'
                ]
            ]);
            Route::resource('ssl', 'Features\SSL\SiteSSLController');
            Route::resource('workers', 'Features\SiteWorkerController');
            Route::resource('repository', 'Repository\SiteRepositoryController');
            Route::resource('hooks', 'Repository\Features\RepositoryHookController');
            Route::resource('ssl-existing', 'Features\SSL\SiteSSLExistingController');
            Route::resource('ssl-lets-encrypt', 'Features\SSL\SiteSSLLetsEncryptController');

            Route::resource('/', 'SiteController', [
                'parameters' => [
                    '' => 'site'
                ]
            ]);
        });
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::group(['prefix' => 'providers', 'namespace' => 'Auth\Providers'], function () {
            Route::resource('server', 'ServerProvidersController');
            Route::resource('repository', 'RepositoryProvidersController');
            Route::resource('notification', 'NotificationProvidersController');
        });
    });

    Route::group(['prefix' => 'server/provider'], function () {
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