<?php

namespace App\Http\Controllers\Api;

use App\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }
  public function index()
  {
    try {
      return response()->json(Holiday::paginate(15));
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
        'start_date' => 'required | date',
        'end_date' => 'required | date',
        'coefficients_salary' => 'required | numeric',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Hãy điền đúng thông tin ngày nghỉ!'
        ], 200);
      }
      $holiday = new Holiday();
      $holiday->name = $request->name;
      $holiday->coefficients_salary = $request->coefficients_salary;
      $holiday->start_date = date('Y-m-d', strtotime($request->start_date));
      $holiday->end_date = date('Y-m-d', strtotime($request->end_date));
      if ($holiday->end_date < $holiday->start_date) {
        return response([
          'status' => false,
          'message' => 'Hãy điền đúng thông tin ngày nghỉ!'
        ], 200);
      }
      $start =  new Carbon($holiday->start_date);
      $end = new Carbon($holiday->end_date);
      $range_date = $end->diffInDays($start) + 1;
      $holiday->range_date = $range_date;
      $holiday->save();
      return response([
        'status' => true,
        'message' => 'Thêm mới ngày nghỉ lễ thành công!'
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }


  public function update(Request $request, Holiday $holiday)
  {
    try {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'start_date' => 'required | date',
        'end_date' => 'required | date',
        'coefficients_salary' => 'required | numeric',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Hãy điền đúng thông tin ngày nghỉ!'
        ], 200);
      }
      $holiday->name = $request->name;
      $holiday->coefficients_salary = $request->coefficients_salary;
      $holiday->start_date = date('Y-m-d', strtotime($request->start_date));
      $holiday->end_date = date('Y-m-d', strtotime($request->end_date));
      if ($holiday->end_date < $holiday->start_date) {
        return response([
          'status' => false,
          'message' => 'Hãy điền đúng thông tin ngày nghỉ!'
        ], 200);
      }
      $start =  new Carbon($holiday->start_date);
      $end = new Carbon($holiday->end_date);
      $range_date = $end->diffInDays($start) + 1;
      $holiday->range_date = $range_date;
      $holiday->save();
      return response([
        'status' => true,
        'message' => 'Cập ngày nghỉ lễ thành công!'
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function show(Request $request, Holiday $holiday)
  {
    try {
      return $holiday;
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function destroy(Holiday $holiday)
  {
    try {
      if ($holiday->delete()) {
        return response([
          'status' => true
        ], 200);
      } else {
        return response([
          'status' => false,
          'message' => 'Xóa ngày nghỉ lễ thất bại!'
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
