<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('events', 'Api\EventController')->only(['store', 'index', 'show']);
Route::post('/events', 'Api\EventController@update');
Route::apiResource('form_requests', 'Api\FormRequestController');
Route::get('/specifiedWorkingTime', 'Api\FormRequestController@specifiedWorkingTime');
