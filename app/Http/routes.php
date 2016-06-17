<?php

Route::auth();

Route::get('/', 'LandingController@getIndex');

Route::group(['middleware' => 'auth'], function() {

    /*
    |--------------------------------------------------------------------------
    | OAuth Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::get('/provider/{provider}/link', 'Auth\OauthController@postNewProvider');
    Route::post('/provider/{provider}/link', 'Auth\OauthController@postNewProvider');
    Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');

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
    Route::get('server/{serverID}/site/{siteID}/deploy', 'SiteController@getDeploy');
    Route::get('server/{serverID}/site/{siteID}/env-file', 'SiteController@getEnv');
    Route::post('server/{serverID}/site/{siteID}/install-repository', 'SiteController@postInstallRepository');
});

