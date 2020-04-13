<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

$router->apiResource('events', 'Api\EventController')->only(['index', 'show']);
$router->post('/events', 'Api\EventController@update');
$router->apiResource('form_requests', 'Api\FormRequestController');
$router->get('/form_requests/users/requests', 'Api\FormRequestController@usersRequests');
$router->post('/form_requests/approve_request', 'Api\FormRequestController@approveRequest');
$router->get('/specifiedWorkingTime', 'Api\FormRequestController@specifiedWorkingTime');

$router->apiResource('fake_face_report', 'Api\FakeFaceReportController')->only(['index', 'destroy', 'store']);

$router->resource('/users', 'Api\UserController');

$router->resource('/permissions', 'Api\PermissionController');

$router->resource('/roles', 'Api\RoleController');

$router->resource('/branches', 'Api\BranchController');

$router->resource('/groups', 'Api\GroupController');

$router->get('/company/getInfo', 'Api\CompanyController@getInfo');