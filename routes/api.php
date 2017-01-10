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
            Route::resource('subscription/invoices', 'Subscription\UserSubscriptionInvoiceController', [
                'except' => [
                    'show',
                ],
            ]);

            Route::get('running-commands', 'UserController@getRunningCommands');
            Route::get('running-deployments', 'UserController@getRunningDeployments');

            Route::resource('ssh-keys', 'UserSshKeyController');
            Route::resource('subscription', 'Subscription\UserSubscriptionController');
            Route::resource('server-providers', 'Providers\UserServerProviderController');
            Route::resource('notification-settings', 'UserNotificationSettingsController');
            Route::resource('repository-providers', 'Providers\UserRepositoryProviderController');
            Route::resource('notification-providers', 'Providers\UserNotificationProviderController');
            Route::resource('subscription/invoice/next', 'Subscription\UserSubscriptionUpcomingInvoiceController');
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

        Route::get('piles/all', 'Pile\PileController@allPiles');
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

        Route::post('server/{server}/find-file', 'Server\ServerFileController@find');

        Route::group(['namespace' => 'Server'], function () {
            Route::group(['prefix' => 'server'], function () {
                Route::post('{server}/file', 'ServerController@getFile');
                Route::post('restore/{server}', 'ServerController@restore');
                Route::post('{server}/file/save', 'ServerController@saveFile');
                Route::post('disk-space/{server}', 'ServerController@getDiskSpace');
                Route::post('restart-server/{server}', 'ServerController@restartServer');
                Route::post('restart-database/{server}', 'ServerController@restartDatabases');
                Route::post('restart-workers/{server}', 'ServerController@restartWorkerServices');
                Route::post('ssh-connection/{server}', 'ServerSshKeyController@testSSHConnection');
                Route::post('restart-web-services/{server}', 'ServerController@restartWebServices');
            });

            Route::resource('servers.file', 'ServerFileController');
            Route::resource('servers.sites', 'ServerSiteController');
            Route::resource('servers.workers', 'ServerWorkerController');
            Route::resource('servers.ssh-keys', 'ServerSshKeyController');
            Route::resource('servers.features', 'ServerFeatureController');
            Route::resource('servers.cron-jobs', 'ServerCronJobController');
            Route::resource('servers.network', 'ServerNetworkRuleController');
            Route::resource('servers.firewall', 'ServerFirewallRuleController');
            Route::resource('servers.provision-steps', 'ServerProvisionStepsController');
        });

        /*
        |--------------------------------------------------------------------------
        | Site Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::resource('sites', 'Site\SiteController');

        Route::post('site/{site}/find-file', 'Site\SiteFileController@find');
        Route::post('site/{site}/reload-file/{file}/server/{server}', 'Site\SiteFileController@reloadFile');

        Route::group(['namespace' => 'Site'], function () {
            Route::group(['prefix' => 'sites'], function () {
                Route::post('deploy', 'SiteController@deploy');
                Route::get('{site}/deployment-steps', 'SiteDeploymentStepsController@getDeploymentSteps');
            });

            Route::post('restart-server/{site}', 'SiteController@restartServer');
            Route::post('restart-database/{site}', 'SiteController@restartDatabases');
            Route::post('restart-workers/{site}', 'SiteController@restartWorkerServices');
            Route::post('restart-web-services/{site}', 'SiteController@restartWebServices');

            Route::resource('sites.file', 'SiteFileController');
            Route::resource('sites.servers', 'SiteServerController');
            Route::resource('sites.workers', 'SiteWorkerController');
            Route::resource('sites.ssh-keys', 'SiteSshKeyController');
            Route::resource('sites.cron-jobs', 'SiteCronJobController');
            Route::resource('sites.ssl-certificate', 'SiteSslController');
            Route::resource('sites.hooks', 'Repository\RepositoryHookController');
            Route::resource('sites.firewall-rules', 'SiteFirewallRuleController');
            Route::resource('v.server-features', 'SiteServerFeaturesController');
            Route::resource('sites.deployment-steps', 'SiteDeploymentStepsController');
        });
    });

    Route::resource('notification-settings', 'NotificationSettingsController');

    Route::get('server/features', 'Server\ServerFeatureController@getFeatures');
    Route::get('server/languages', 'Server\ServerFeatureController@getLanguages');
    Route::get('server/frameworks', 'Server\ServerFeatureController@getFrameworks');
    Route::get('site/{site}/editable-files', 'Site\SiteFeatureController@getEditableFiles');
    Route::get('server/{server}/editable-files', 'Server\ServerFeatureController@getEditableFiles');
    Route::get('site/{site}/framework/editable-files', 'Site\SiteFeatureController@getEditableFrameworkFiles');

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
