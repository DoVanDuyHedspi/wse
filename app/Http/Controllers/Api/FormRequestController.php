<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\FormRequest;
use App\Helpers\EventHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FormRequestController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $request)
  {
    $filter = $request->query();
    if (count($filter)) {
      $forms = Auth::user()->form_requests()->when($filter["status"], function ($query, $status) {
        return $query->where("status", $status);
      })->when($filter["month"], function ($query, $month) {
        return $query->where("created_at", "like", '%' . date('Y-m', strtotime($month)) . '%');
      })->with('user')->orderBy('created_at', 'desc')->get();
    } else {
      $forms = Auth::user()->form_requests()->with('user')->orderBy('created_at', 'desc')->get();
    }

    return $forms;
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'user_code' => 'required',
      'type' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ thông tin!'
      ], 200);
    }
    $form = new FormRequest();
    $form->user_code = $request->user_code;
    $form->status = 'waiting';
    $form->type = $request->type;
    $form->reason = $request->reason;
    $result = self::validateFormRequest($form, $request);
    $form = $result['form'];
    $message = $result['message'];
    if (strlen($message) != 0) {
      return response([
        'status' => false,
        'message' => $message
      ], 200);
    }
    $form->save();
    return response([
      'status' => true
    ], 200);
  }


  public function show(FormRequest $formRequest)
  {
    $formUpdate = [];
    $leave_begin = '';
    $leave_end = '';
    $work_begin = '';
    $work_end = '';
    if ($formRequest->leave_date) {
      $leave_begin = date('d-m-Y', strtotime($formRequest->leave_date)) . " " . $formRequest->leave_time_begin;
    }
    if ($formRequest->leave_date) {
      $leave_end = date('d-m-Y', strtotime($formRequest->leave_date)) . " " . $formRequest->leave_time_end;
    }
    if ($formRequest->work_date) {
      $work_begin = date('d-m-Y', strtotime($formRequest->work_date)) . " " . $formRequest->work_time_begin;
    }
    if ($formRequest->work_date) {
      $work_end = date('d-m-Y', strtotime($formRequest->work_date)) . " " . $formRequest->work_time_end;
    }
    $formUpdate['leave_time_begin'] = $leave_begin;
    $formUpdate['leave_time_end'] = $leave_end;
    $formUpdate['work_time_begin'] = $work_begin;
    $formUpdate['work_time_end'] = $work_end;
    $formUpdate['reason'] = $formRequest->reason;
    $formUpdate['range_time'] = $formRequest->range_time;
    $formUpdate['type'] = $formRequest->type;
    $formUpdate['user_code'] = $formRequest->user_code;
    $formUpdate['user_group'] = $formRequest->user->group_id;
    $formUpdate['user_name'] = $formRequest->user->name;
    $formUpdate['user_branch'] = $formRequest->user->branch_id;
    $formUpdate['id'] = $formRequest->id;
    return $formUpdate;
  }

  public function update(Request $request, FormRequest $formRequest)
  {
    $validator = Validator::make($request->all(), [
      'user_code' => 'required',
      'type' => 'required',
      'id' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ thông tin!'
      ], 200);
    }
    $form = FormRequest::where('id', $request->id)->where('type', $request->type)->first();
    if ($form == null) {
      return response([
        'status' => false,
        'message' => 'Thông tin của yêu cầu không chính xác!'
      ], 200);
    }
    $form->type = $request->type;
    $form->reason = $request->reason;
    if (isset($request->range_time)) {
      $form->range_time = $request->range_time;
    }

    $result = self::validateFormRequest($form, $request);
    $form = $result['form'];
    $message = $result['message'];
    if (strlen($message) != 0) {
      return response([
        'status' => false,
        'message' => $message
      ], 200);
    }
    $form->save();
    return response([
      'status' => true
    ], 200);
  }

  public function destroy(FormRequest $formRequest)
  {
    if ($formRequest->user_code != Auth::user()->employee_code) {
      return response([
        'status' => false,
        'message' => "Bạn không có quyền xóa yêu cầu của người này!"
      ], 200);
    }
    $formRequest->delete();
    return response([
      'status' => true
    ], 200);
  }

  public function specifiedWorkingTime()
  {
    $specified_working_time = [];
    $specified_working_time['morning_begin'] = config('wse.morning_begin');
    $specified_working_time['morning_late'] = config('wse.morning_late');
    $specified_working_time['morning_end'] = config('wse.morning_end');
    $specified_working_time['afternoon_begin'] = config('wse.afternoon_begin');
    $specified_working_time['afternoon_late'] = config('wse.afternoon_late');
    $specified_working_time['afternoon_end'] = config('wse.afternoon_end');
    return response()->json($specified_working_time);
  }

  static function validateFormRequest($form, $request)
  {
    $message = '';
    $morning_late = date('H:i', strtotime(config('wse.morning_late')));
    $afternoon_late = date('H:i', strtotime(config('wse.afternoon_late')));
    $morning_begin = date('H:i', strtotime(config('wse.morning_begin')));
    $afternoon_begin = date('H:i', strtotime(config('wse.afternoon_begin')));
    $morning_end = date('H:i', strtotime(config('wse.morning_end')));
    $afternoon_end = date('H:i', strtotime(config('wse.afternoon_end')));
    if ($request->type == 'ILM') {
      $form->leave_time_begin = $morning_begin;
      $form->leave_time_end = date('H:i', strtotime($request->leave_time_end));
      $form->leave_date = date('Y-m-d', strtotime($request->leave_time_end));
      if ($form->leave_time_end < $form->leave_time_begin) {
        $message = $message . "Đây là form xin đi muộn, thời gian đến của bạn không chính xác. \n";
      } else if ($form->leave_time_end > $morning_late) {
        $message = $message . "Lượng thời gian của bạn không thể lớn hơn thời gian cho phép tối đa. \n";
      }
    } else if ($request->type == 'ILA') {
      $form->leave_time_begin = $afternoon_begin;
      $form->leave_time_end = date('H:i', strtotime($request->leave_time_end));
      $form->leave_date = date('Y-m-d', strtotime($request->leave_time_end));
      if ($form->leave_time_end < $form->leave_time_begin) {
        $message = $message . "Đây là form xin đi muộn, thời gian đến của bạn không chính xác. \n";
      } else if ($form->leave_time_end > $afternoon_late) {
        $message = $message . "Lượng thời gian của bạn không thể lớn hơn thời gian cho phép tối đa. \n";
      }
    } else if ($request->type == 'LEM') {
      $form->leave_time_begin = date('H:i', strtotime($request->leave_time_begin));
      $form->leave_time_end = $morning_end;
      $form->leave_date = date('Y-m-d', strtotime($request->leave_time_begin));
      if ($form->leave_time_begin > $form->leave_time_end) {
        $message = $message . "Đây là form xin về sơm, thời gian đến của bạn không chính xác. \n";
      } else if ($morning_late > $form->leave_time_begin) {
        $message = $message . "Lượng thời gian của bạn không thể lớn hơn thời gian cho phép tối đa. \n";
      }
    } else if ($request->type == 'LEA') {
      $form->leave_time_begin = date('H:i', strtotime($request->leave_time_begin));
      $form->leave_time_end = $afternoon_end;
      $form->leave_date = date('Y-m-d', strtotime($request->leave_time_begin));
      if ($form->leave_time_begin > $form->leave_time_end) {
        $message = $message . "Đây là form xin về sơm, thời gian đến của bạn không chính xác. \n";
      } else if ($afternoon_late > $form->leave_time_begin) {
        $message = $message . "Lượng thời gian của bạn không thể lớn hơn thời gian cho phép tối đa. \n";
      }
    } else if ($request->type == 'LO') {
      $form->leave_time_begin = date('H:i', strtotime($request->leave_time_begin));
      $form->leave_time_end = date('H:i', strtotime($request->leave_time_end));
      $form->leave_date = date('Y-m-d', strtotime($request->leave_time_begin));
      if ($form->leave_time_begin > $morning_begin) {
        $message = $message . "Thời gian đăng ký của bạn không thể sớm hơn thời gian bắt đầu làm việc của công ty. \n";
      }
      if ($form->leave_time_end > $afternoon_end) {
        $message = $message . "Thời gian đăng ký của bạn không thể muộn hơn thời gian kết thúc làm việc của công ty. \n";
      }
      if ($form->leave_date != date('Y-m-d', strtotime($request->leave_time_end))) {
        $message = $message . "Thời gian làm đăng ký phải cùng trong một ngày. \n";
      }
    } else if ($request->type == 'QQD') {
      $form->work_time_begin = date('H:i', strtotime($request->work_time_begin));
      $form->work_date = date('Y-m-d', strtotime($request->work_time_begin));
    } else if ($request->type == 'QQV') {
      $form->work_time_end = date('H:i', strtotime($request->work_time_end));
      $form->work_date = date('Y-m-d', strtotime($request->work_time_end));
    } else if ($request->type == 'QQF') {

      $form->work_time_begin = date('H:i', strtotime($request->work_time_begin));
      $form->work_time_end = date('H:i', strtotime($request->work_time_end));
      $form->work_date = date('Y-m-d', strtotime($request->work_time_begin));
      if ($form->work_time_begin > $form->work_time_end) {
        $message = $message . "Yêu cầu thời gian về phải lớn hơn thời gian đến. \n";
      }
      if (date('Y-m-d', strtotime($request->work_time_begin)) != date('Y-m-d', strtotime($request->work_time_end))) {
        $message = $message . "Thời gian làm đăng ký phải cùng trong một ngày. \n";
      }
    } else if ($request->type == 'RM' || $request->type == 'OT') {
      $form->work_time_begin = date('H:i', strtotime($request->work_time_begin));
      $form->work_time_end = date('H:i', strtotime($request->work_time_end));
      $form->work_date = date('Y-m-d', strtotime($request->work_time_begin));
      if (date('Y-m-d', strtotime($request->work_time_begin)) != date('Y-m-d', strtotime($request->work_time_end))) {
        $message = $message . "Thời gian làm đăng ký phải cùng trong một ngày. \n";
      }
      if ($form->work_time_begin > $form->work_time_end) {
        $message = $message . "Yêu cầu thời gian kết thúc phải lớn hơn thời gian bắt đầu. \n";
      }
      if (($request->type == 'OT') && ($form->work_time_begin < $afternoon_end)) {
        $message = $message . "Thời gian làm bù không được bắt đầu trong thời gian làm việc chính thức. \n";
      }
      if (($request->type == 'RM') && ($form->work_time_begin < $morning_begin)) {
        $message = $message . "Thời gian làm sớm hơn thời gian bắt đầu làm việc chính thức. \n";
      }
      $form->range_time = $request->range_time;
    }

    if (in_array($form->type, ['ILM', 'ILA', 'LEM', 'LEA', 'LO'])) {
      $form->work_time_begin = date('H:i', strtotime($request->work_time_begin));
      $form->work_time_end = date('H:i', strtotime($request->work_time_end));
      $form->work_date = date('Y-m-d', strtotime($request->work_time_begin));
      if (date('Y-m-d', strtotime($request->work_time_begin)) != date('Y-m-d', strtotime($request->work_time_end))) {
        $message = $message . "Thời gian làm đăng ký phải cùng trong một ngày. \n";
      }
      if ($form->work_time_begin > $form->work_time_end) {
        $message = $message . "Yêu cầu thời gian về phải lớn hơn thời gian đến. \n";
      }
      if ($form->work_time_begin < $afternoon_end) {
        $message = $message . "Thời gian làm bù không được bắt đầu trong thời gian làm việc chính thức. \n";
      }
      $range_time = (strtotime($request->work_time_end) - strtotime($request->work_time_begin)) / 60;
      $form->range_time = $range_time;
      if ($range_time < $request->range_time) {
        $message = $message . "Thời gian làm bù không đủ. \n";
      }
      if ($form->work_date < $form->leave_date) {
        $message = $message . "Thời gian làm bù không được trong quá khứ. \n";
      }
    }
    if (in_array($form->type, ['ILM', 'ILA', 'LEM', 'LEA', 'LO', 'OT'])) {
      $forms_created = FormRequest::where('work_date', $form->work_date)->whereIn('type',['ILM', 'ILA', 'LEM', 'LEA', 'LO', 'OT'])->get();
      foreach ($forms_created as $created_form) {

        if (((isset($request->id) && $request->id != $created_form->id)) || !isset($request->id)) {

          $created_begin = date('H:i', strtotime($created_form->work_time_begin));
          $created_end = date('H:i', strtotime($created_form->work_time_end));
          $validate = false;
          if ($form->work_time_begin >= $created_end || $form->work_time_end <= $created_begin) {
            $validate = true;
          }
          if ($validate == false) {
            $message = $message . "Thời gian đăng ký đã bị trùng thời gian của yêu cầu khác. \n";
          }
        }
      }
    }
    return ['form' => $form, 'message' => $message];
  }

  public function usersRequests(Request $request)
  {
    if (Auth::user()->can('check-requests') || Auth::user()->can('approve-requests')) {
      $filter = $request->query();
      if (count($filter)) {
        $forms = FormRequest::with('user')->when($filter["status"], function ($query, $status) {
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
        $forms = FormRequest::with('user')->orderBy('created_at', 'desc')->get();
      }
      // dd($filter["date_begin"]);
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
    $form = FormRequest::find($request->request_id);
    if ($form == null) {
      return response([
        'status' => false,
        'message' => 'Yêu cầu này không tồn tại'
      ], 200);
    }
    if (in_array($request->action, ['forward', 'cancel']) && Auth::user()->can('check-requests')) {
      $form->status = $request->action;
      $form->save();
      return response([
        'status' => true
      ], 200);
    }
    if (in_array($request->action, ['accept', 'refuse']) && Auth::user()->can('approve-requests')) {
      $form->status = $request->action;
      
      if ($request->action == 'accept') {
        
        $message = self::acceptRequest($form);
        if (strlen($message) != 0) {
          return response([
            'status' => false,
            'message' => $message
          ], 200);
        }
      }
      $form->save();
      return response([
        'status' => true
      ], 200);
    }
    return response([
      'status' => false,
      'message' => 'Bạn không có quyền kiểm tra, duyệt yêu cầu'
    ], 200);
  }

  static function acceptRequest($request)
  {
    $message = '';
    if (in_array($request->type, ['ILM', 'ILA', 'LEM', 'LEA', 'LO'])) {
      $event_work = Event::whereDate('date', '=', date('Y-m-d', strtotime($request->work_date)))->first();
      $event_leave = Event::whereDate('date', '=', date('Y-m-d', strtotime($request->leave_date)))->first();
      if ($event_work) {
        $time_in = date('H:i', strtotime($event_work->time_in));
        $time_out = date('H:i', strtotime($event_work->time_out));
        $work_begin = date('H:i', strtotime($request->work_time_begin));
        $work_end = date('H:i', strtotime($request->work_time_end));
        if ($time_in <= $work_begin && $time_out >= $work_end) {
          if ($request->type == 'ILM') {
            $event_leave->ILM = 0;
          } else if ($request->type == 'LEM') {
            $event_leave->LEM = 0;
          } else if ($request->type == 'ILA') {
            $event_leave->ILA = 0;
          } else if ($request->type == 'LEA') {
            $event_leave->LEA = 0;
          }

          $has_error = $event_leave->ILM + $event_leave->LEM + $event_leave->ILA + $event_leave->LEA;
          if ($has_error == 0) {
            $event_leave->status = 2;
          }
          $event_leave->fined_time -= $request->range_time;
          $event_leave->save();
        } else {
          $message = $message  . "Nhân viên chưa làm bù đủ giờ\n";
        }
      } else {
        $message = $message . "Nhân viên chưa làm bù\n";
      }
    } else if (in_array($request->type, ['QQD', 'QQV', 'QQF'])) {
      $event_work = Event::whereDate('date', '=', date('Y-m-d', strtotime($request->work_date)))->first();
      if ($request->type == 'QQF') {
        $event_work->time_in = date('H:i', strtotime($request->work_time_begin));
        $event_work->time_out = date('H:i', strtotime($request->work_time_end));
      } else if ($request->type == 'QQV') {
        $event_work->time_out = date('H:i', strtotime($request->work_time_end));
      } else {
        if ($event_work->time_out) {
          $event_work->time_in = date('H:i', strtotime($request->work_time_begin));
        } else {
          $event_work->time_out =  $event_work->time_in;
          $event_work->time_in = date('H:i', strtotime($request->work_time_begin));
        }
      }
      $ev_update = EventHelper::updateEventInfo($event_work);
      $ev_update->save();
    } else if ($request->type == 'OT') {
      $new_event = new Event();
      $new_event->date = date('Y-m-d', strtotime($request->work_date));
      $new_event->time_in = date('H:i', strtotime($request->work_time_begin));
      $new_event->time_out = date('H:i', strtotime($request->work_time_end));
      $new_event->user_code = $request->user_code;
      $new_event->status = 1;
      $ev_update = EventHelper::updateEventInfo($new_event);
      $ev_update->save();
    }
    return $message;
  }
}
