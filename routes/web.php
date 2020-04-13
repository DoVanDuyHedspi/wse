<?php

Route::get('/', function () {
  $user = request()->user(); //lấy kèm với role của user (dùng cho vue router sau này)
  $avatar = $user->getFirstMediaUrl('avatar');
  $user['avatar'] = $avatar;
  return view('welcome', ['user' => $user]);
})->middleware('auth');

Auth::routes();

// Route::resource('/users', 'UserController');

// Route::resource('/permissions', 'PermissionController');

// Route::resource('/roles', 'RoleController');

// Route::resource('/branches', 'BranchController');

// Route::resource('/groups', 'GroupController');

// Route::get('/company/getInfo', 'CompanyController@getInfo');
