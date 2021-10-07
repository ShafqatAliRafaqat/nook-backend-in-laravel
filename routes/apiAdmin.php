<?php

Route::group(['middleware' => ['api', 'auth:api'], 'prefix' => $v1Prefix . '/admin'], function () {


    // dashboard
    Route::get('dashboard', 'Admin\APIDashboardController@index');
    
    // notices
    Route::get('notices', 'Admin\APINoticeAdminController@index');
    Route::put('notices/{id}', 'Admin\APINoticeAdminController@edit');

    // receipts
    Route::get('receipts', 'Admin\APIAdminReceiptController@index');
    Route::post('receipts', 'Admin\APIAdminReceiptController@generateReceipt');
    Route::post('update_receipt/{id}', 'Admin\APIAdminReceiptController@update');
    Route::post('receipts/publish', 'Admin\APIAdminReceiptController@publishReceipt');
    Route::post('receipts/pay/{id}', 'Admin\APIAdminReceiptController@payReceipt');
    Route::delete('receipt/{id}', 'Admin\APIAdminReceiptController@delete');

    // bookings
    Route::get('bookings', 'Admin\APIBookingAdminController@index');
    Route::post('bookings', 'Admin\APIBookingAdminController@add');
    Route::post('bookings/{id}', 'Admin\APIBookingAdminController@update');
    Route::post('bookings/addSecurity/{id}', 'Admin\APIBookingAdminController@addSecurity');

    // shifts
    Route::get('shifts', 'Admin\APIShiftAdminController@index');
    Route::post('shifts/{id}', 'Admin\APIShiftAdminController@edit');
   
    // Room shifts
    Route::get('room_shifts', 'Admin\APIRoomShiftAdminController@index');
    Route::post('room_shifts/{id}', 'Admin\APIRoomShiftAdminController@edit');

    // nooks
    Route::get('nooks', 'Admin\APINookAdminController@index');
    Route::get('all_nooks', 'Admin\APINookAdminController@allNooks');
    Route::post('nooks', 'Admin\APINookAdminController@add');
    Route::put('nooks/{id}', 'Admin\APINookAdminController@edit');

    // notifications
    Route::get('notifications', 'Admin\APIAdminNotificationController@index');
    Route::post('notifications', 'Admin\APIAdminNotificationController@add');


    // Complaints
    Route::get('complaints', 'Admin\APIComplaintAdminController@index');
    Route::post('complaints', 'Admin\APIComplaintAdminController@add');
    Route::put('complaints/{id}', 'Admin\APIComplaintAdminController@edit');

    // transaction
    Route::get('transactions', 'Admin\APITransactionAdminController@index');
    Route::post('transactions/{id}', 'Admin\APITransactionAdminController@update');

    // settings
    Route::get('settings', 'Admin\APISettingAdminController@index');
    Route::put('settings', 'Admin\APISettingAdminController@edit');

    // user
    Route::get('users', 'Admin\APIUserAdminController@index');
    Route::get('all_users', 'Admin\APIUserAdminController@allUsers');
    Route::post('create_user', 'Admin\APIUserAdminController@createUser');
    Route::put('users/{id}', 'Admin\APIUserAdminController@edit');

    // Role
    Route::get('roles', 'Admin\APIRoleAdminController@index');
    Route::get('roles/user/{id}', 'Admin\APIRoleAdminController@userRoles');
    Route::post('roles', 'Admin\APIRoleAdminController@create');
    Route::patch('roles/user/{id}', 'Admin\APIRoleAdminController@updateRoles');

    // permissions
    Route::get('permissions', 'Admin\APIPermissionAdminController@index');
    Route::get('permissions/role/{id}', 'Admin\APIPermissionAdminController@rolePermissions');
    Route::get('permissions/user/{id}', 'Admin\APIPermissionAdminController@userPermissions');
    Route::patch('permissions/role/{id}', 'Admin\APIPermissionAdminController@updatePermissions');
    Route::post('permissions', 'Admin\APIPermissionAdminController@create');


    // medias

    Route::get('medias', 'Admin\APIMediaAdminController@index');
    Route::get('medias/{id}', 'Admin\APIMediaAdminController@show');
    Route::post('medias', 'Admin\APIMediaAdminController@create');
    // do not change method to put or patch, for details see
    // https://stackoverflow.com/questions/50691938/patch-and-put-request-does-not-working-with-form-data
    Route::post('medias/{id}', 'Admin\APIMediaAdminController@edit');
    Route::delete('medias/{id}', 'Admin\APIMediaAdminController@delete');

    // Area
    Route::get('area', 'Admin\APIAreaAdminController@index');
    Route::post('area', 'Admin\APIAreaAdminController@add');
    Route::get('area/{id}', 'Admin\APIAreaAdminController@edit');
    Route::post('area/{id}', 'Admin\APIAreaAdminController@update');
    Route::delete('area/{id}', 'Admin\APIAreaAdminController@delete');

});
