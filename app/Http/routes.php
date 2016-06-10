<?php

Route::auth();

Route::get('/', 'LandingController@getIndex');

Route::group(['middleware' => 'auth'], function() {

});

