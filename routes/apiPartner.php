<?php

Route::group(['middleware' => ['api', 'auth:api'], 'prefix' => $v1Prefix . '/admin'], function () {

    // Partner Nooks
    Route::get('partner/all_nooks', 'Partner\APIPartnerNookController@index');
    Route::post('partner/nooks', 'Partner\APIPartnerNookController@add');
    Route::put('partner/nooks/{id}', 'Partner\APIPartnerNookController@edit');
    Route::get('partner/delete_nook/{id}', 'Partner\APIPartnerNookController@delete');
    Route::get('partner/area', 'Partner\APIPartnerNookController@area');

    // Partner Complaints
    Route::get('partner/complaints', 'Partner\APIPartnerComplaintController@index');
    Route::post('partner/complaints/{id}', 'Partner\APIPartnerComplaintController@edit');
    Route::post('partner/complaints','Partner\APIPartnerComplaintController@add');
    
    // notices
    Route::get('partner/notices', 'Partner\APIPartnerNoticeController@index');
    Route::post('partner/notices/{id}', 'Partner\APIPartnerNoticeController@edit');

    // receipts
    Route::get('partner/receipts', 'Partner\APIPartnerReceiptController@index');
    Route::post('partner/receipts', 'Partner\APIPartnerReceiptController@generateReceipt');
    Route::post('partner/receipts/publish', 'Partner\APIPartnerReceiptController@publishReceipt');
    Route::post('partner/receipts/pay/{id}', 'Partner\APIPartnerReceiptController@payReceipt');
    
    // shifts
    Route::get('partner/shifts', 'Partner\APIPartnerShiftController@index');
    Route::post('partner/shifts/{id}', 'Partner\APIPartnerShiftController@edit');

    // Room shifts
    Route::get('partner/room_shifts', 'Partner\APIPartnerRoomShiftController@index');
    Route::post('partner/room_shifts/{id}', 'Partner\APIPartnerRoomShiftController@edit');

    // bookings
    Route::get('partner/bookings', 'Partner\APIPartnerBookingController@index');
    Route::post('partner/bookings/{id}', 'Partner\APIPartnerBookingController@update');
    Route::post('partner/bookings/addSecurity/{id}', 'Partner\APIPartnerBookingController@addSecurity');

    // transaction
    Route::get('partner/transactions', 'Partner\APIPartnerTransactionController@index');
    Route::post('partner/transactions/{id}', 'Partner\APIPartnerTransactionController@update');
    
    // visits
    Route::get('partner/visits','Partner\APIPartnerVisitsController@index');
    Route::post('partner/visits/{id}','Partner\APIPartnerVisitsController@update');

});
