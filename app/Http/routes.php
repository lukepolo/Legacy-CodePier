<?php

Route::auth();

Route::get('/', 'LandingController@getIndex');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/my-profile', 'Auth\UserController@getMyProfile');

    Route::post('/provider/link', 'Auth\OauthController@postNewProvider');
    Route::get('/provider/{provider}/callback', 'Auth\OauthController@getHandleProviderCallback');
    
     /*
    |--------------------------------------------------------------------------
    | Server Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::post('create-server', 'ServerController@postCreateServer');
});

