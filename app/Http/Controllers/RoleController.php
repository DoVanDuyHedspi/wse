<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
// use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth'); //bắt buộc khi sử dụng phải đăng nhập
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->user()->can('view-roles')) {
      $permissions = Permission::all();
      $roles = Role::with('permissions')->paginate(10);
      $response['permissions'] = $permissions;
      $response['roles'] = $roles;
      return response()->json($response);
    }

    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view roles!'
    ], 200);
  }

  public function store(Request $request)
  {
    if ($request->user()->can('create-roles')) {
      $validator = Validator::make($request->all(), [
        'slug' => 'required',
        'name' => 'required',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Tạo vai trò mới thất bại!'
        ], 200);
      }
      $role = new Role();
      $role->slug = $request->slug;
      $role->name = $request->name;
      if ($role->save()) {
        if ($request->permissions) {
          foreach ($request->permissions as $perId) {
            $permission = Permission::find($perId);
            $role->permissions()->save($permission);
          }
        }
        return response()->json($role);
      }
    }
    return response([
      'status' => false,
      'message' => 'Xin lỗi bạn không có quyền tạo vai trò!'
    ], 200);
  }

  public function update(Request $request, $id)
  {
    if ($request->user()->can('update-roles')) {
      $validator = Validator::make($request->all(), [
        'slug' => 'required',
        'name' => 'required',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Sửa quyền thất bại!'
        ], 200);
      }

      $role = Role::find($id);
      if ($role) {
        $role->slug = $request->slug;
        $role->name = $request->name;
        $role->save();
        $list_pers = [];
        $permissions = $role->permissions()->get();
        if ((bool) $role->permissions()->count()) {
          foreach ($permissions as $permission) {
            array_push($list_pers, $permission->id);
          }
        }
        $add_pers = array_diff($request->permissions, $list_pers);
        $dell_pers = array_diff($list_pers, $request->permissions);
        foreach ($add_pers  as $perId) {
          $permission = Permission::find($perId);
          $role->permissions()->save($permission);
        }
        foreach ($dell_pers  as $perId) {
          $permission = Permission::find($perId);
          $role->permissions()->detach($permission);
        }
        $response['permissions'] = $role->permissions()->get();
        $response['role'] = $role;
        return response()->json($response);
      }
    }
  }

  public function destroy(Request $request, $id)
  {
    if ($request->user()->can('delete-roles')) {
      $role = Role::find($id);
      if ($role->delete()) {
        return response([
          'status' => true
        ], 200);
      } else {
        return response([
          'status' => false,
          'message' => 'Xóa vai trò thất bại!'
        ], 200);
      }
    }

    return response([
      'status' => false,
      'message' => 'Bạn không có quyền xóa vai tr!'
    ], 200);
  }
}
