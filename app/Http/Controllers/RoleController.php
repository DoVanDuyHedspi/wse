<?php

namespace App\Http\Controllers;

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
    if ($request->user()->hasRole('manager')) {
      return response()->json(Role::paginate(20));
    }

    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view roles!'
    ], 200);
  }

  public function store(Request $request)
  {
    if ($request->user()->hasRole('manager')) {
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
      $role->slug = $request->input('slug');
      $role->name = $request->input('name');
      if ($role->save()) {
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
    if ($request->user()->hasRole('manager')) {
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
        $role->slug = $request->input('slug');
        $role->name = $request->input('name');
        $role->save();
        return response()->json($role);
      }
    }
  }

  public function destroy(Request $request, $id)
  {
    if ($request->user()->hasRole('manager')) {
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
