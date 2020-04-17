<?php
use App\User;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;

$router->get('/', function () {
  $user = User::where('id',request()->user()->id)->first(); 
  $avatar = $user->getFirstMediaUrl('avatar');
  $user['avatar'] = $avatar;
  $user['roles'] = $user->roles;
  $user['all_permissions'] = $user->getAllPermissionsOfUser(); 
  // dd($user['permissions']);
  return view('welcome', ['user' => $user]);
})->middleware('auth');

Auth::routes();

// Route::resource('/users', 'UserController');

// Route::resource('/permissions', 'PermissionController');

// Route::resource('/roles', 'RoleController');

// Route::resource('/branches', 'BranchController');

// Route::resource('/groups', 'GroupController');

// Route::get('/company/getInfo', 'CompanyController@getInfo');
