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
    Route::post('server/{serverID}/ssh-key/install', 'ServerController@postInstallSshKey');
    Route::get('server/{serverID}/ssh-key/{serverSshKeyId}/remove', 'ServerController@getRemoveSshKey');

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

    Route::post('server/{serverID}/site/{siteID}/domain/rename', 'SiteController@postRenameDomain');
    Route::post('server/{serverID}/site/{siteID}/ssl/lets-encrypt', 'SiteController@postRequestLetsEncryptSSLCert');

    Route::post('server/{serverID}/site/{siteID}/env', 'SiteController@postEnv');

});

