<?php

Route::auth();

Route::get('/', 'HomeController@getIndex');

Route::group(['middleware' => 'auth'], function() {

});

