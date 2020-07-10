<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Storage;

$router->apiResource('events', 'Api\EventController')->only(['index', 'show']);
$router->post('/events', 'Api\EventController@update');
$router->get('/events/daily/checkinout', 'Api\EventController@dailyCheckInOut');

$router->apiResource('form_requests', 'Api\FormRequestController');
$router->get('/form_requests/users/requests', 'Api\FormRequestController@usersRequests');
$router->get('/form_requests/users/ot_rm', 'Api\FormRequestController@usersOtRmRequests');
$router->post('/form_requests/users/ot_rm/confirm', 'Api\FormRequestController@confirmOtRmRequests');
$router->post('/form_requests/approve_request', 'Api\FormRequestController@approveRequest');

$router->apiResource('form_leaves', 'Api\FormLeaveController');
$router->get('/form_leave/manage/requests', 'Api\FormLeaveController@usersRequests');
$router->post('/form_leave/manage/approve', 'Api\FormLeaveController@approveRequest');
$router->post('/form_leave/manage/getInfo', 'Api\FormLeaveController@getInfo');

$router->get('/specifiedWorkingTime', 'Api\FormRequestController@specifiedWorkingTime');

$router->apiResource('form_complain', 'Api\FormComplainController');
$router->get('/form_complain/manage/requests', 'Api\FormComplainController@usersRequests');
$router->post('/form_complain/manage/approve', 'Api\FormComplainController@approveRequest');
$router->post('/form_complain/manage/approved_form', 'Api\FormComplainController@approvedForm');
$router->get('/video/googleDrive', 'Api\FormComplainController@getVideoFromGgDrive');

$router->apiResource('fake_face_report', 'Api\FakeFaceReportController')->only(['index', 'destroy', 'store']);

$router->resource('/users', 'Api\UserController');
$router->get('/users/{id}/notifications', 'Api\UserController@notification');
$router->post('/users/notifications/{id}/read', 'Api\UserController@markAsReadNoti');
$router->post('/users/export/users/', 'Api\UserController@exportCsv');
$router->post('/users/export/shiftwork/', 'Api\UserController@exportShiftwork');
$router->post('/users/export/inLateLeaveEarly/', 'Api\UserController@exportInLateLeaveEarly');
$router->post('/users/export/checkInOut/', 'Api\UserController@exportCheckInOut');
$router->post('/users/export/timesheetsEmployee/', 'Api\UserController@exportTimesheetsEmployee');

$router->resource('/permissions', 'Api\PermissionController');

$router->resource('/roles', 'Api\RoleController');

$router->resource('/branches', 'Api\BranchController');

$router->resource('/groups', 'Api\GroupController');

$router->resource('/positions', 'Api\PositionController')->only(['store', 'update', 'destroy']);

$router->get('/company/getInfo', 'Api\CompanyController@getInfo');
$router->post('/company/settingTimekeeping', 'Api\CompanyController@settingTimekeeping');
$router->get('/company/getTimekeeping', 'Api\CompanyController@getTimekeeping');
$router->get('/company/timeUpdateTimekeepingData', 'Api\CompanyController@timeUpdateTimekeepingData');

$router->resource('/holiday', 'Api\HolidayController')->only(['index', 'store', 'show', 'update', 'destroy']);

$router->resource('/leave_type', 'Api\LeaveTypeController')->only(['index', 'store', 'show', 'update', 'destroy']);
