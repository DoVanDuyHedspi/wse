<?php

Route::get('/', function () {
  $user = request()->user()->roles[0]; //lấy kèm với role của user (dùng cho vue router sau này)
  return view('welcome', ['user' => $user]);
})->middleware('auth');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('/users', 'UserController');

Route::resource('/permissions', 'PermissionController');

Route::resource('/roles', 'RoleController');

Route::resource('/branches', 'BranchController');

Route::resource('/groups', 'GroupController');