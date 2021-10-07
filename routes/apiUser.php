<?php

// secured api using user's access token

Route::group(['namespace'=>'Auth','middleware' => ['api','auth:api'], 'prefix' => $v1Prefix.'/auth'], function () {

    // auth
    Route::post('logout', 'APIAuthController@logout');
    Route::post('refresh', 'APIAuthController@refresh');

    Route::post('user/nooks', 'APINookController@add');
    Route::get('user/area', 'APINookController@area');
    // user
    Route::get('user','APIUserController@details');
    Route::patch('user','APIUserController@update');
    Route::patch('user/changePassword','APIUserController@changePassword');

    // profile
    Route::get('user/nook','APINookController@nook');
    Route::post('user/nook/review','APINookController@addReview');

    // bookings
    Route::get('user/bookings','APIBookingsController@index');
    Route::post('user/bookings','APIBookingsController@add'); 
    Route::post('user/bookings/cancel','APIBookingsController@cancel'); 
    
    // notifications
    Route::get('user/notifications','APINotificationsController@index');

    // visits
    Route::get('user/visits','APIVisitsController@index');
    Route::post('user/visits','APIVisitsController@add');
    Route::post('user/visits/cancel','APIVisitsController@cancel');

    // complains
    Route::get('user/complains','APIComplainsController@index');
    Route::post('user/complains','APIComplainsController@add');

    // Notices
    Route::get('user/notices','APINoticesController@index');
    Route::post('user/notices','APINoticesController@add');
    Route::post('user/notices/cancel','APINoticesController@cancel');

    // receipts
    Route::get('user/receipts','APIReceiptController@index');

    // transactions
    Route::get('user/transactions','APITransactionsController@index');
    Route::post('user/transactions','APITransactionsController@add');


    // shifts
    Route::get('user/shifts','APIShiftController@index');
    Route::post('user/shifts','APIShiftController@add');
    Route::post('user/shifts/cancel','APIShiftController@cancel');

    // Room shifts
    Route::get('user/room_shifts','APIRoomShiftController@index');
    Route::post('user/room_shifts','APIRoomShiftController@add');
    Route::post('user/room_shifts/cancel','APIRoomShiftController@cancel');

    // DeliveryBoy
    Route::get('deliveryBoy/getOrderToDeliver','APIDeliveryBoyController@getOrderToDeliver');
    Route::patch('deliveryBoy/editOrder','APIDeliveryBoyController@editOrder');
    Route::post('deliveryBoy/transactions','APIDeliveryBoyController@payCash');


    // Number Verification
    Route::post('user/numberVerification/send','APINumberVerificationController@send');
    Route::post('user/numberVerification/verify','APINumberVerificationController@verify');

    // reviews
    Route::post('user/reviews','APIReviewController@add');
});
