<?php

Route::get('', 'Server\CustomServerProvisioningController@provision');
Route::post('start/{provisioning_key}', 'Server\CustomServerProvisioningController@callback');
Route::get('keys/{provisioning_key}/public', 'Server\CustomServerProvisioningController@returnPublicKey');
Route::get('keys/{provisioning_key/private}', 'Server\CustomServerProvisioningController@returnPrivateKey');
Route::get('end/{provisioning_key}', 'Server\CustomServerProvisioningController@end');
