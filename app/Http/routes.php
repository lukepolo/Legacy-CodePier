<?php

Route::auth();

Route::get('/', 'LandingController@getIndex');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/my-profile', 'Auth\UserController@getMyProfile');

    Route::get('/provider/{provider}/link', 'Auth\OauthController@postNewProvider');
    Route::post('/provider/{provider}/link', 'Auth\OauthController@postNewProvider');
    
    Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');
    
    /*
    |--------------------------------------------------------------------------
    | Server Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::get('server/{serverID}', 'ServerController@getServer');
    Route::post('create-server', 'ServerController@postCreateServer');

    /*
    |--------------------------------------------------------------------------
    | Site Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::get('server/{serverID}/site/{siteID}', 'SiteController@getSite');
    Route::get('server/{serverID}/site/{siteID}/env-file', 'SiteController@getEnv');
});

