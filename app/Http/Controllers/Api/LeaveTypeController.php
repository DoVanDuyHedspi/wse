<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveType;
use Exception;
use Illuminate\Support\Facades\Validator;


class LeaveTypeController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index()
  {
    try {
      return response()->json(LeaveType::all());
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function store(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required',
        'description' => 'required',
        'has_salary' => 'required',
        'has_accumulated' => 'required',
        'type' => 'required',
        'number_days' => 'required | numeric',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Hãy điền đúng thông tin kiểu nghỉ phép!'
        ], 200);
      }
      $leave_type = new LeaveType();
      $leave_type->name = $request->name;
      $leave_type->slug = $request->slug;
      $leave_type->description = $request->description;
      $leave_type->type = $request->type;
      $leave_type->number_days = $request->number_days;
      $leave_type->has_salary = $request->has_salary;
      $leave_type->has_accumulated = $request->has_accumulated;
      if($request->expiry_date) {
        $leave_type->expiry_date = date('Y-m-d', strtotime($request->expiry_date));
      }
      
      $leave_type->save();
      return response([
        'status' => true,
        'message' => 'Thêm mới kiểu nghỉ phép thành công!'
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function update(Request $request, LeaveType $leave_type)
  {
    try {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required',
        'description' => 'required',
        'has_salary' => 'required',
        'has_accumulated' => 'required',
        'type' => 'required',
        'number_days' => 'required | numeric',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Hãy điền đúng thông tin kiểu nghỉ phép!'
        ], 200);
      }
      $leave_type->name = $request->name;
      $leave_type->slug = $request->slug;
      $leave_type->description = $request->description;
      $leave_type->type = $request->type;
      $leave_type->number_days = $request->number_days;
      $leave_type->has_salary = $request->has_salary;
      $leave_type->has_accumulated = $request->has_accumulated;
      if($request->expiry_date) {
        $leave_type->expiry_date = date('Y-m-d', strtotime($request->expiry_date));
      } else {
        $leave_type->expiry_date = null;
      }
      
      $leave_type->save();
      return response([
        'status' => true,
        'message' => 'Cập nhật kiểu nghỉ phép thành công!'
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function show(Request $request, LeaveType $leave_type)
  {
    try {
      return $leave_type;
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function destroy(LeaveType $leave_type)
  {
    try {
      if ($leave_type->delete()) {
        return response([
          'status' => true
        ], 200);
      } else {
        return response([
          'status' => false,
          'message' => 'Xóa kiểu nghỉ phép thất bại!'
        ], 200);
      }
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }
}
