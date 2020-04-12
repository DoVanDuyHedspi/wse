<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('events', 'Api\EventController')->only(['index', 'show']);
Route::post('/events', 'Api\EventController@update');
Route::apiResource('form_requests', 'Api\FormRequestController');
Route::get('/form_requests/users/requests', 'Api\FormRequestController@usersRequests');
Route::post('/form_requests/approve_request', 'Api\FormRequestController@approveRequest');
Route::get('/specifiedWorkingTime', 'Api\FormRequestController@specifiedWorkingTime');

// Route::post('/fake_face_report', 'Api\FakeFaceReportController@store');
// Route::get('/fake_face_report', 'Api\FakeFaceReportController@index');
// Route::delete('/fake_face_report/{id}/', 'Api\FakeFaceReportController@destroy');

Route::apiResource('fake_face_report', 'Api\FakeFaceReportController')->only(['index', 'destroy', 'store']);