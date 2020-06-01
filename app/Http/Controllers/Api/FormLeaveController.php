<?php

namespace App\Http\Controllers\Api;

use App\FormLeave;
use App\LeaveType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FormLeaveController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $request)
  {
    try {
      $filter = $request->query();
      if (count($filter)) {
        $forms = Auth::user()->form_leaves()->with('leave_type')->when($filter["status"], function ($query, $status) {
          return $query->where("status", $status);
        })->when($filter["month"], function ($query, $month) {
          return $query->where("created_at", "like", '%' . date('Y-m', strtotime($month)) . '%');
        })->with('user')->orderBy('created_at', 'desc')->get();
      } else {
        $forms = Auth::user()->form_leaves()->with('user')->with('leave_type')->orderBy('created_at', 'desc')->get();
      }

      return $forms;
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
        'user_code' => 'required',
        'begin_leave_date' => 'required | date',
        'end_leave_date' => 'required | date',
        'reason' => 'required',
        'leave_type_id' => 'required | numeric'
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Hãy nhập đủ thông tin!'
        ], 200);
      }
      $message = "";
      $form = new FormLeave();
      $form->user_code = $request->user_code;
      $form->status = 'waiting';
      $begin_leave_date = date('Y-m-d', strtotime($request->begin_leave_date));
      $end_leave_date = date('Y-m-d', strtotime($request->end_leave_date));
      if ($end_leave_date < $begin_leave_date) {
        $message = $message . "\n" . "Ngày kết thúc nghỉ phải cùng ngày hoặc sau ngày bắt đầu nghỉ";
      }
      $form->begin_leave_date = $begin_leave_date;
      $form->end_leave_date = $end_leave_date;
      $start =  new Carbon($form->begin_leave_date);
      $end = new Carbon($form->end_leave_date);
      $number_days = $end->diffInDays($start) + 1;
      $form->number_days = $number_days;
      $form->reason = $request->reason;
      $form->leave_type_id = $request->leave_type_id;
      if (strlen($message) != 0) {
        return response([
          'status' => false,
          'message' => $message
        ], 200);
      }
      $form->save();
      return response([
        'status' => true,
        'message' => 'Gửi yêu cầu nghỉ phép thành công'
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function show($id)
  {
    try {
      $form = FormLeave::find($id);
      $formUpdate = [];
      $formUpdate['user_code'] = $form->user_code;
      $formUpdate['id'] = $form->id;
      $formUpdate['reason'] = $form->reason;
      $formUpdate['leave_type_id'] = $form->leave_type_id;
      $formUpdate['begin_leave_date'] = date('d-m-Y', strtotime($form->begin_leave_date));
      $formUpdate['end_leave_date'] = date('d-m-Y', strtotime($form->end_leave_date));
      $formUpdate['user_name'] = $form->user->name;
      return $formUpdate;
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $form = FormLeave::find($id);
      $validator = Validator::make($request->all(), [
        'user_code' => 'required',
        'begin_leave_date' => 'required | date',
        'end_leave_date' => 'required | date',
        'reason' => 'required',
        'leave_type_id' => 'required | numeric'
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Hãy nhập đủ thông tin!'
        ], 200);
      }
      $message = "";
      $form->user_code = $request->user_code;
      $begin_leave_date = date('Y-m-d', strtotime($request->begin_leave_date));
      $end_leave_date = date('Y-m-d', strtotime($request->end_leave_date));
      if ($end_leave_date < $begin_leave_date) {
        $message = $message . "\n" . "Ngày kết thúc nghỉ phải cùng ngày hoặc sau ngày bắt đầu nghỉ";
      }
      $form->begin_leave_date = $begin_leave_date;
      $form->end_leave_date = $end_leave_date;
      $start =  new Carbon($form->begin_leave_date);
      $end = new Carbon($form->end_leave_date);
      $number_days = $end->diffInDays($start) + 1;
      $form->number_days = $number_days;
      $form->reason = $request->reason;
      $form->leave_type_id = $request->leave_type_id;
      if (strlen($message) != 0) {
        return response([
          'status' => false,
          'message' => $message
        ], 200);
      }
      $form->save();
      return response([
        'status' => true,
        'message' => 'Cập nhật yêu cầu nghỉ phép thành công'
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function destroy($id)
  {
    try {
      $formLeave = FormLeave::find($id);
      if ($formLeave->user_code != Auth::user()->employee_code) {
        return response([
          'status' => false,
          'message' => "Bạn không có quyền xóa yêu cầu của người này!"
        ], 200);
      }
      $formLeave->delete();
      return response([
        'status' => true
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function usersRequests(Request $request)
  {
    if (Auth::user()->can('check-requests') || Auth::user()->can('approve-requests')) {
      $filter = $request->query();
      if (count($filter)) {
        $forms = FormLeave::with('user')->with('leave_type')->when($filter["status"], function ($query, $status) {
          return $query->where("status", $status);
        })->when($filter["date_begin"], function ($query, $date_begin) {
          return $query->whereDate("created_at", '>=', date('Y-m-d H:i:s', strtotime($date_begin)));
        })->when($filter["date_end"], function ($query, $date_end) {
          return $query->whereDate("created_at", '<=', date('Y-m-d H:i:s', strtotime($date_end)));
        })->whereHas('user', function ($query) use ($filter) {
          return $query->when($filter["group_id"], function ($q, $group_id) {
            return $q->where("group_id", $group_id);
          })->when($filter["branch_id"], function ($q, $branch_id) {
            return $q->where("branch_id", $branch_id);
          })->when($filter["search"], function ($q, $search) {
            return $q->where(function ($qr) use ($search) {
              $qr->where("name", "like", '%' . $search . '%')->orWhere("employee_code", "like", '%' . $search . '%');
            });
          });
        })->orderBy('created_at', 'desc')->get();
      } else {
        $forms = FormLeave::with('user')->with('leave_type')->orderBy('created_at', 'desc')->get();
      }
      return $forms;
    }
    return response(['status' => false, 'message' => 'Bạn không có quyền kiểm tra hoặc duyệt yêu cầu của nhân viên'], 200);
  }

  public function approveRequest(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'action' => 'required',
      'request_id' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ thông tin!'
      ], 200);
    }
    $form = FormLeave::find($request->request_id);
    if ($form == null) {
      return response([
        'status' => false,
        'message' => 'Yêu cầu này không tồn tại'
      ], 200);
    }
    if (in_array($request->action, ['accept', 'refuse']) && Auth::user()->can('approve-requests')) {
      $form->status = $request->action;
      $form->save();
      // $form->user->notify(new ResultOfRequest($form));
      return response([
        'status' => true
      ], 200);
    }
    return response([
      'status' => false,
      'message' => 'Bạn không có quyền kiểm tra, duyệt yêu cầu'
    ], 200);
  }
}
