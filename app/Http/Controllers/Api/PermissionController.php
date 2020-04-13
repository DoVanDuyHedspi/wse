<?php

namespace App\Http\Controllers\Api;

use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->user()->can('view-permissions')) {
      return response()->json(Permission::paginate(10));
    }

    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view permissions!'
    ], 200);
  }

  public function store(Request $request)
  {
    if ($request->user()->can('create-permissions')) {
      $validator = Validator::make($request->all(), [
        'slug' => 'required',
        'name' => 'required',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Tạo quyền mới thất bại!'
        ], 200);
      }
      $permission = new Permission();
      $permission->slug = $request->input('slug');
      $permission->name = $request->input('name');
      if ($permission->save()) {
        return response()->json($permission);
      }
    }
    return response([
      'status' => false,
      'message' => 'Xin lỗi bạn không có quyền tạo quyền này!'
    ], 200);
  }

  public function update(Request $request, $id)
  {
    if($request->user()->can('update-permissions')) {
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

      $permission = Permission::find($id);
      if($permission) {
        $permission->slug = $request->input('slug');
        $permission->name = $request->input('name');
        $permission->save();
        return response()->json($permission);
      }
    }
  }

  public function destroy(Request $request, $id)
  {
    if ($request->user()->can('delete-permissions')) {
      $permission = Permission::find($id);
      if ($permission->delete()) {
        return response([
          'status' => true
        ], 200);
      } else {
        return response([
          'status' => false,
          'message' => 'Xóa quyền thất bại!'
        ], 200);
      }
    }

    return response([
      'status' => false,
      'message' => 'Bạn không có quyền xóa quyền!'
    ], 200);
  }
}
