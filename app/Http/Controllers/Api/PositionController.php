<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Position;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api'); //bắt buộc khi sử dụng phải đăng nhập
  }

  public function store(Request $request)
  {
    if ($request->user()->can('update-organization')) {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'begin_allowed_access' => 'required | regex:/(\d+\:\d+)/',
        'end_allowed_access' => 'required | regex:/(\d+\:\d+)/',
      ]);
      if ($validator->fails()) {

        return response([
          'status' => false,
          'message' => 'Tạo vị trí mới thất bại!'
        ], 200);
      }
      $position = new Position();
      $position->name = $request->name;
      $position->begin_allowed_access = $request->begin_allowed_access;
      $position->end_allowed_access = $request->end_allowed_access;
      $position->save();
      return response(['status' => true, 'position' => $position]);
    }

    return response([
      'status' => false,
      'message' => 'Xin lỗi bạn không có quyền tạo vị trí mới!'
    ], 200);
  }
}
