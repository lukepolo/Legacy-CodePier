<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    /*
 |--------------------------------------------------------------------------
 | User Routes
 |--------------------------------------------------------------------------
 |
 */
    Route::resource('me', 'User\UserController', [
        'only' => [
            'index',
        ],
    ]);

    Route::resource('user', 'User\UserController', [
        'except' => 'index',
    ]);

    Route::group(['prefix' => 'my'], function () {
        Route::group(['namespace' => 'User'], function () {
            Route::resource('subscription/invoices', 'Subscription\UserSubscriptionInvoiceController');
            Route::resource('subscription', 'Subscription\UserSubscriptionController');
            Route::resource('subscription/invoice/next', 'Subscription\UserSubscriptionUpcomingInvoiceController');

            Route::resource('ssh-keys', 'UserSshKeyController');
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
        Route::post('change-pile', 'Pile\PileController@changePile');

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
                Route::post('{server}/file', 'ServerController@getFile');
                Route::post('{server}/file/save', 'ServerController@saveFile');
                Route::post('disk-space/{server}', 'ServerController@getDiskSpace');
                Route::post('restart-server/{server}', 'ServerController@restartServer');
                Route::post('restart-database/{server}', 'ServerController@restartDatabases');
                Route::post('restart-workers/{server}', 'ServerController@restartWorkerServices');
                Route::post('ssh-connection/{server}', 'ServerSshKeyController@testSSHConnection');
                Route::post('restart-web-services/{server}', 'ServerController@restartWebServices');
            });

            Route::resource('servers.provision-steps', 'ServerProvisionStepsController');
            Route::resource('servers.features', 'ServerFeatureController');
            Route::resource('servers.cron-jobs', 'ServerCronJobController');
            Route::resource('servers.workers', 'ServerWorkerController');
            Route::resource('servers.firewall', 'ServerFirewallRuleController');
            Route::resource('servers.network', 'ServerNetworkRuleController');
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

        Route::post('site/{site}/find-file', 'Site\SiteFileController@find');
        Route::post('site/{site}/update/server-features', 'Site\SiteController@updateSiteServerFeatures');

        Route::group(['namespace' => 'Site'], function () {
            Route::group(['prefix' => 'sites'], function () {
                Route::post('deploy', 'SiteController@deploy');
            });

            Route::resource('site.file', 'SiteFileController');
            Route::resource('site.servers', 'SiteServerController');
            Route::resource('site.workers', 'SiteWorkerController');
            Route::resource('site.ssh-keys', 'SiteSshKeyController');
            Route::resource('site.cron-jobs', 'SiteCronJobController');
            Route::resource('site.hooks', 'Repository\RepositoryHookController');
            Route::resource('site.certificate', 'SiteSSLController');
            Route::resource('site.firewall-rules', 'SiteFirewallRuleController');
            Route::resource('site.repository', 'Repository\SiteRepositoryController');
            Route::resource('site.certificate-existing', 'Certificate\SiteSSLExistingController');
            Route::resource('site.certificate-lets-encrypt', 'Certificate\SiteSSLLetsEncryptController');
        });
    });

    Route::get('server/languages', 'Server\ServerFeatureController@getLanguages');
    Route::get('server/frameworks', 'Server\ServerFeatureController@getFrameworks');
    Route::get('server/features', 'Server\ServerFeatureController@getServerFeatures');
    Route::get('server/{server}/editable-files', 'Server\ServerFeatureController@getEditableServerFiles');
    Route::get('site/{site}/framework/editable-files', 'Server\ServerFeatureController@getEditableFrameworkFiles');

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
            'namespace' => 'Server\Providers\DigitalOcean',
        ], function () {
            Route::resource('options', 'DigitalOceanServerOptionsController');
            Route::resource('regions', 'DigitalOceanServerRegionsController');
            Route::resource('features', 'DigitalOceanServerFeaturesController');
        });
    });
});
