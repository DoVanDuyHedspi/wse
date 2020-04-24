<?php

namespace App\Http\Controllers\Api;

use App\FormComplain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FormComplainController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $request)
  {
    $filter = $request->query();
    if (count($filter)) {
      $forms = Auth::user()->form_complains()->when($filter["status"], function ($query, $status) {
        return $query->where("status", $status);
      })->when($filter["month"], function ($query, $month) {
        return $query->where("created_at", "like", '%' . date('Y-m', strtotime($month)) . '%');
      })->with('user')->orderBy('created_at', 'desc')->get();
    } else {
      $forms = Auth::user()->form_complains()->with('user')->orderBy('created_at', 'desc')->get();
    }

    return $forms;
  }

  public function usersRequests(Request $request)
  {
    if (Auth::user()->can('check-requests') || Auth::user()->can('approve-requests')) {
      $filter = $request->query();
      if (count($filter)) {
        $forms = FormComplain::with('user')->when($filter["status"], function ($query, $status) {
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
        $forms = FormComplain::with('user')->orderBy('created_at', 'desc')->get();
      }
      return $forms;
    }
    return response(['status' => false, 'message' => 'Bạn không có quyền kiểm tra hoặc duyệt yêu cầu của nhân viên'], 200);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'date' => 'required | date',
      'begin_time' => 'required | regex:/(\d+\:\d+)/',
      'end_time' => 'required | regex:/(\d+\:\d+)/',
      'note' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ và đúng thông tin!'
      ], 200);
    }
    $form = new FormComplain();
    $form->begin_time = date('H:i', strtotime($request->begin_time));
    $form->end_time = date('H:i', strtotime($request->end_time));
    if ($form->end_time >= $form->begin_time) {
      return response([
        'status' => false,
        'message' => 'Thời điểm kết thúc phải sau thời điểm bắt đầu'
      ], 200);
    }
    $form->user_code = $request->user()->employee_code;
    $form->date = date('Y-m-d', strtotime($request->date));
    $form->note = $request->note;
    $form->status = 'waiting';
    $form->result = 'waiting';
    $form->save();
    return response(['status' => true]);
  }

  public function show(FormComplain $formComplain)
  {
    if ($formComplain->user_code != Auth::user()->employee_code) {
      return response([
        'status' => false,
        'message' => "Bạn không có quyền chỉnh sửa yêu cầu của người này!"
      ], 200);
    }
    $formComplain['user_group'] = $formComplain->user->group_id;
    $formComplain['user_name'] = $formComplain->user->name;
    $formComplain['user_branch'] = $formComplain->user->branch_id;
    return $formComplain;
  }

  public function update(Request $request, FormComplain $formComplain)
  {
    if ($formComplain->user_code != Auth::user()->employee_code) {
      return response([
        'status' => false,
        'message' => "Bạn không có quyền chỉnh sửa yêu cầu của người này!"
      ], 200);
    }
    $validator = Validator::make($request->all(), [
      'date' => 'required | date',
      'begin_time' => 'required | regex:/(\d+\:\d+)/',
      'end_time' => 'required | regex:/(\d+\:\d+)/',
      'note' => 'required',
      'id' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ thông tin!'
      ], 200);
    }
    $form = FormComplain::where('id', $request->id)->first();
    if ($form == null) {
      return response([
        'status' => false,
        'message' => 'Yêu cầu này không tồn tại!'
      ], 200);
    }
    $form->begin_time = date('H:i', strtotime($request->begin_time));
    $form->end_time = date('H:i', strtotime($request->end_time));
    if ($form->end_time <= $form->begin_time) {
      return response([
        'status' => false,
        'message' => 'Thời điểm kết thúc phải sau thời điểm bắt đầu'
      ], 200);
    }
    $form->date = date('Y-m-d', strtotime($request->date));
    $form->note = $request->note;
    $form->save();
    return response([
      'status' => true
    ], 200);
  }

  public function destroy(FormComplain $formComplain)
  {
    if ($formComplain->user_code != Auth::user()->employee_code) {
      return response([
        'status' => false,
        'message' => "Bạn không có quyền xóa yêu cầu của người này!"
      ], 200);
    }
    $formComplain->delete();
    return response([
      'status' => true
    ], 200);
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
    $form = FormComplain::find($request->request_id);
    if ($form == null) {
      return response([
        'status' => false,
        'message' => 'Yêu cầu này không tồn tại'
      ], 200);
    }
    $form->status = $request->action;
    if ($form->status == 'refuse') {
      $form->result = 'fail';
    }
    $form->save();
    return response(['status' => true, 'form_complain' => $form], 200);
  }

}
