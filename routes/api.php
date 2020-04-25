<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Storage;

$router->apiResource('events', 'Api\EventController')->only(['index', 'show']);
$router->post('/events', 'Api\EventController@update');
$router->apiResource('form_requests', 'Api\FormRequestController');
$router->get('/form_requests/users/requests', 'Api\FormRequestController@usersRequests');
$router->post('/form_requests/approve_request', 'Api\FormRequestController@approveRequest');
$router->get('/specifiedWorkingTime', 'Api\FormRequestController@specifiedWorkingTime');

$router->apiResource('form_complain', 'Api\FormComplainController');
$router->get('/form_complain/manage/requests', 'Api\FormComplainController@usersRequests');
$router->post('/form_complain/manage/approve', 'Api\FormComplainController@approveRequest');
$router->post('/form_complain/manage/check_camera', 'Api\FormComplainController@checkCamera');

$router->apiResource('fake_face_report', 'Api\FakeFaceReportController')->only(['index', 'destroy', 'store']);

$router->resource('/users', 'Api\UserController');

$router->resource('/permissions', 'Api\PermissionController');

$router->resource('/roles', 'Api\RoleController');

$router->resource('/branches', 'Api\BranchController');

$router->resource('/groups', 'Api\GroupController');

$router->resource('/positions', 'Api\PositionController')->only(['store', 'update', 'destroy']);

$router->get('/company/getInfo', 'Api\CompanyController@getInfo');
$router->post('/company/settingTimekeeping', 'Api\CompanyController@settingTimekeeping');
$router->get('/company/getTimekeeping', 'Api\CompanyController@getTimekeeping');


