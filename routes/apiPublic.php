<?php

// public api
Route::group(['middleware' => 'api', 'prefix' => $v1Prefix.'/auth'], function () {
    Route::post('login', 'APILoginRegisterController@login');
    Route::post('socialLogin', 'APILoginRegisterController@socialLogin');
    Route::post('register', 'APILoginRegisterController@register');
    Route::post('user_register', 'APILoginRegisterController@register');
});

Route::group(['middleware' => 'api','prefix' => $v1Prefix ], function () {

    // password reset controller

    Route::post('passwordsReset/send','APIPasswordResetController@send');
    Route::post('passwordsReset/change','APIPasswordResetController@change');

    // nooks
    Route::get('nooks','APINookController@index');

    // contacts
    Route::post('contacts','APIContactController@save');

    // migrate
    Route::get('migrate', 'Admin\APINoticeAdminController@migrate');

});

