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
    Route::apiResource('me', 'User\UserController', [
        'only' => [
            'index',
        ],
    ]);

    Route::apiResource('user', 'User\UserController', [
        'except' => 'index',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Categories Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::apiResource('categories', 'CategoriesController');

    /*
    |--------------------------------------------------------------------------
    | Buoy App Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::get('buoy-apps/buoyClasses', 'BuoyAppController@getBuoyClasses');
    Route::post('buoy-apps/{buoy_app}/update', 'BuoyAppController@update');
    Route::apiResource('buoy-apps', 'BuoyAppController');

    /*
    |--------------------------------------------------------------------------
    | Bitts Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::apiResource('bitts', 'BittsController');
    Route::post('bitt/{bitt}/run', 'BittsController@runOnServers');

    /*
    |--------------------------------------------------------------------------
    | System Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::get('systems', 'SystemsController@index');

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::group(['prefix' => 'my'], function () {
        Route::group(['namespace' => 'User'], function () {
            Route::apiResource('subscription/invoices', 'Subscription\UserSubscriptionInvoiceController', [
                'except' => [
                    'show',
                ],
            ]);

            Route::get('running-commands', 'UserController@getRunningCommands');
            Route::get('running-deployments', 'UserController@getRunningDeployments');

            Route::apiResource('ssh-keys', 'UserSshKeyController');
            Route::apiResource('subscription', 'Subscription\UserSubscriptionController');
            Route::apiResource('server-providers', 'Providers\UserServerProviderController');
            Route::apiResource('notification-settings', 'UserNotificationSettingsController');
            Route::apiResource('repository-providers', 'Providers\UserRepositoryProviderController');
            Route::apiResource('notification-providers', 'Providers\UserNotificationProviderController');
            Route::apiResource('subscription/invoice/next', 'Subscription\UserSubscriptionUpcomingInvoiceController');
        });

        /*
        |--------------------------------------------------------------------------
        | Events Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::apiResource('events', 'EventController');

        /*
        |--------------------------------------------------------------------------
        | Teamwork Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::apiResource('team', 'User\Team\UserTeamController');

        Route::group(['prefix' => 'team', 'namespace' => 'User\Team'], function () {
            Route::apiResource('team.members', 'UserTeamMemberController');
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
        Route::apiResource('piles', 'Pile\PileController');
        Route::apiResource('pile.sites', 'Pile\PileSitesController');

        Route::post('change-pile', 'Pile\PileController@changePile');

        /*
       |--------------------------------------------------------------------------
       | Server Routes
       |--------------------------------------------------------------------------
       |
       */

        Route::apiResource('servers', 'Server\ServerController');

        Route::post('server/{server}/find-file', 'Server\ServerFileController@find');
        Route::post('server/{server}/reload-file/{file}', 'Server\ServerFileController@reloadFile');

        Route::post('server/{server}/custom', 'Server\ServerController@generateCustomServerSh')->middleware('scope:create-custom-server');

        Route::get('all_servers/buoys', 'Server\ServerBuoyController@myServerBuoys');

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

            Route::apiResource('servers.file', 'ServerFileController');
            Route::apiResource('servers.buoys', 'ServerBuoyController');
            Route::apiResource('servers.sites', 'ServerSiteController');
            Route::apiResource('servers.workers', 'ServerWorkerController');
            Route::apiResource('servers.schemas', 'ServerSchemaController');
            Route::apiResource('servers.ssh-keys', 'ServerSshKeyController');
            Route::apiResource('servers.features', 'ServerFeatureController');
            Route::apiResource('servers.cron-jobs', 'ServerCronJobController');
            Route::apiResource('servers.ssl-certificate', 'ServerSslController');
            Route::apiResource('servers.network', 'ServerNetworkRuleController');
            Route::apiResource('servers.firewall-rules', 'ServerFirewallRuleController');
            Route::apiResource('servers.provision-steps', 'ServerProvisionStepsController');
            Route::apiResource('servers.language-settings', 'ServerLanguageSettingsController');
            Route::apiResource('servers.environment-variables', 'ServerEnvironmentVariablesController');
            Route::get('server/{server}/language-settings', 'ServerLanguageSettingsController@getLanguageSettings');
        });

        /*
        |--------------------------------------------------------------------------
        | Site Routes
        |--------------------------------------------------------------------------
        |
        */

        Route::apiResource('sites', 'Site\SiteController');

        Route::post('site/{site}/find-file', 'Site\SiteFileController@find');
        Route::post('site/{site}/refresh-ssh-keys', 'Site\SiteController@refreshPublicKey');
        Route::post('site/{site}/refresh-deploy-key', 'Site\SiteController@refreshDeployKey');
        Route::post('site/{site}/reload-file/{file}/server/{server}', 'Site\SiteFileController@reloadFile');

        Route::group(['namespace' => 'Site'], function () {
            Route::post('deploy/{site}', 'SiteController@deploy');
            Route::post('rollback/{site}', 'SiteController@rollback');
            Route::get('site/{site}/deployment-steps', 'SiteDeploymentStepsController@getDeploymentSteps');

            Route::post('restart-server/{site}', 'SiteController@restartServer');
            Route::post('restart-database/{site}', 'SiteController@restartDatabases');
            Route::post('restart-workers/{site}', 'SiteController@restartWorkerServices');
            Route::post('restart-web-services/{site}', 'SiteController@restartWebServices');

            Route::apiResource('sites.file', 'SiteFileController');
            Route::apiResource('sites.buoys', 'SiteBuoyController');
            Route::apiResource('sites.servers', 'SiteServerController');
            Route::apiResource('sites.workers', 'SiteWorkerController');
            Route::apiResource('sites.schemas', 'SiteSchemaController');
            Route::apiResource('sites.ssh-keys', 'SiteSshKeyController');
            Route::apiResource('sites.cron-jobs', 'SiteCronJobController');
            Route::apiResource('sites.ssl-certificate', 'SiteSslController');
            Route::apiResource('sites.deployments', 'SiteDeploymentsController');
            Route::apiResource('sites.hooks', 'Repository\RepositoryHookController');
            Route::apiResource('sites.firewall-rules', 'SiteFirewallRuleController');
            Route::apiResource('sites.server-features', 'SiteServerFeaturesController');
            Route::apiResource('sites.deployment-steps', 'SiteDeploymentStepsController');
            Route::apiResource('sites.language-settings', 'SiteLanguageSettingsController');
            Route::apiResource('sites.environment-variables', 'SiteEnvironmentVariablesController');
            Route::get('site/{site}/language-settings', 'SiteLanguageSettingsController@getLanguageSettings');
        });
    });

    Route::apiResource('notification-settings', 'NotificationSettingsController');

    Route::apiResource('server/types', 'Server\ServerTypesController');
    Route::get('server/features', 'Server\ServerFeatureController@getFeatures');
    Route::get('server/languages', 'Server\ServerFeatureController@getLanguages');
    Route::get('server/frameworks', 'Server\ServerFeatureController@getFrameworks');
    Route::get('site/{site}/editable-files', 'Site\SiteFeatureController@getEditableFiles');
    Route::get('server/{server}/editable-files', 'Server\ServerFeatureController@getEditableFiles');
    Route::get('site/{site}/framework/editable-files', 'Site\SiteFeatureController@getEditableFrameworkFiles');

    Route::group(['prefix' => 'auth'], function () {
        Route::group(['prefix' => 'providers', 'namespace' => 'Auth\Providers'], function () {
            Route::apiResource('server-providers', 'ServerProvidersController');
            Route::apiResource('repository-providers', 'RepositoryProvidersController');
            Route::apiResource('notification-providers', 'NotificationProvidersController');
        });
    });

    Route::group(['prefix' => 'server/providers'], function () {
        Route::group([
            'prefix' => \App\Http\Controllers\Auth\OauthController::DIGITAL_OCEAN,
            'namespace' => 'Server\Providers\DigitalOcean',
        ], function () {
            Route::apiResource('options', 'DigitalOceanServerOptionsController');
            Route::apiResource('regions', 'DigitalOceanServerRegionsController');
            Route::apiResource('features', 'DigitalOceanServerFeaturesController');
        });
    });
});
