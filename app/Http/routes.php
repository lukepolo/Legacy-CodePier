<?php

Route::auth();

Route::get('/', 'LandingController@getIndex');

/*
|--------------------------------------------------------------------------
| OAuth Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/provider/{provider}/link', 'Auth\OauthController@newProvider');
Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');

Route::group(['middleware' => 'auth'], function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::get('/admin', 'AdminController@getIndex');
    Route::get('/admin/server-provider/{providerID}/options-regions', 'AdminController@getServerOptionsAndRegions');

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::get('/my-profile', 'Auth\UserController@getMyProfile');
    Route::post('/my-profile', 'Auth\UserController@postMyProfile');
    Route::post('/my-profile/add-ssh-key', 'Auth\UserController@postAddSshKey');
    Route::get('/my-profile/remove-ssh-key/{sshKeyID}', 'Auth\UserController@getRemoveSshKey');

    /*
    |--------------------------------------------------------------------------
    | Server Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::get('server/{serverID}', 'ServerController@getServer');
    Route::post('create-server', 'ServerController@postCreateServer');

    Route::get('servers/archive', 'ServerController@getArchivedServers');
    Route::get('server/{serverID}/archive', 'ServerController@getArchiveServer');
    Route::get('servers/archive/{serverID}/activate', 'ServerController@getActivateArchivedServer');


    Route::get('server/{serverID}/check-connection', 'ServerController@getTestSshConnection');

    Route::post('server/{serverID}/ssh-key/install', 'ServerController@postInstallSshKey');
    Route::get('server/{serverID}/ssh-key/{serverSshKeyId}/remove', 'ServerController@getRemoveSshKey');

    Route::post('server/{serverID}/cron-job/install', 'ServerController@postInstallCronJob');
    Route::get('server/{serverID}/cron-job/{cronJobID}/remove', 'ServerController@getRemoveCronJob');

    Route::post('server/{serverID}/firewall-rule/add', 'ServerController@postAddFirewallRule');
    Route::post('server/{serverID}/server-network-rules/add', 'ServerController@postAddServerNetworkRules');
    Route::get('server/{serverID}/firewall-rule/{fireWallID}/remove', 'ServerController@getRemoveFireWallRule');

    Route::post('server/{serverID}/daemon/add', 'ServerController@postAddDaemon');
    Route::get('server/{serverID}/daemon/{daemonID}/remove', 'ServerController@getRemoveDaemon');


    Route::get('server/{serverID}/restart/server', 'ServerController@getRestartServer');
    Route::get('server/{serverID}/restart/workers', 'ServerController@getRestartWorkers');
    Route::get('server/{serverID}/restart/database', 'ServerController@getRestartDatabase');
    Route::get('server/{serverID}/restart/web-server', 'ServerController@getRestartWebServices');

    /*
    |--------------------------------------------------------------------------
    | Site Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::get('server/{serverID}/site/{siteID}', 'SiteController@getSite');
    Route::post('server/{serverID}/create-site', 'SiteController@postCreateSite');

    Route::get('server/{serverID}/site/{siteID}/deploy', 'SiteController@getDeploy');

    Route::get('server/{serverID}/site/{siteID}/env-file', 'SiteController@getEnv');

    Route::post('server/{serverID}/site/{siteID}/install-repository', 'SiteController@postInstallRepository');

    Route::get('server/{serverID}/site/{siteID}/ssl/remove', 'SiteController@getRemoveSSL');
    Route::post('server/{serverID}/site/{siteID}/domain/rename', 'SiteController@postRenameDomain');
    Route::post('server/{serverID}/site/{siteID}/ssl/lets-encrypt', 'SiteController@postRequestLetsEncryptSSLCert');

    Route::post('server/{serverID}/site/{siteID}/env', 'SiteController@postEnv');

    Route::post('server/{serverID}/site/{siteID}/install-worker', 'SiteController@postInstallWorker');
    Route::get('server/{serverID}/site/{siteID}/remove-worker/{workerID}', 'SiteController@getRemoveWorker');

});

