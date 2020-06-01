<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Helpers\EventHelper;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource as EventResource;
use App\Http\Resources\UsersEventsResource;
use App\User;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $request)
  {
    $user = Auth::user();
    if ($user->can('update-timesheets')) {
      $year = date('Y');
      $month = date('m');
      $filter = $request->query();


      if (count($filter)) {
        $employees = User::getRelationships()->filter($filter)->get();
        if ($filter['month']) {
          $year = date('Y', strtotime($filter['month']));
          $month = date('m', strtotime($filter['month']));
        }
      } else {
        $employees = User::all();
      };
      $allDayOfMonth = UserHelper::getAllDayOfMonth($year, $month);
      foreach ($employees as $employee) {
        $events = [];
        $number_working_days = 0;
        $ILM = 0;
        $LEM = 0;
        $ILA = 0;
        $LEA = 0;
        foreach ($allDayOfMonth as $date) {
          if (date('Y-m-d') >= date('Y-m-d', strtotime($date))) {
            $event = Event::where('user_code', $employee->employee_code)->where('date', $date)->first();
            if ($event == null) {
              array_push($events, [
                'time_in' => '',
                'time_out' => '',
                'status' => '',
                'type' => null,
                'date' => date('d-m-Y', strtotime($date)),
              ]);
            } else {
              if ($event->type == 1 || $event->type == 2) {
                $number_working_days += 0.5;
              } else if ($event->type == 3) {
                $number_working_days += 1;
              }
              $ILM += $event->ILM;
              $LEM += $event->LEM;
              $ILA += $event->ILA;
              $LEA += $event->LEA;
              array_push($events,  [
                'time_in' => date('H:i', strtotime($event->time_in)),
                'time_out' => $event->time_out ? date('H:i', strtotime($event->time_out)) : '--:--',
                'status' => $event->status,
                'type' => $event->type,
                'date' => date('d-m-Y', strtotime($date)),
              ]);
            }
          }
        }
        $employee['events'] = $events;
        $employee['ILM'] = $ILM;
        $employee['LEM'] = $LEM;
        $employee['ILA'] = $ILA;
        $employee['LEA'] = $LEA;
        $employee['number_working_days'] = $number_working_days;
      }
      return UsersEventsResource::collection($employees);
    };
    return response([
      'status' => false,
      'message' => 'Người dùng này không có quyền xem bảng chấm công của nhân viên!'
    ], 200);
  }

  public function show(Request $request, $id)
  {
    $date = $request->query()['date'];
    // $date = date('Y-m', strtotime($date));
    $year = date('Y', strtotime($date));
    $month = date('m', strtotime($date));
    $events = [];
    $allDayOfMonth = UserHelper::getAllDayOfMonth($year, $month);
    foreach ($allDayOfMonth as $date) {
      $classes = 'ktc';
      $isHoliday = UserHelper::isHoliday($date);
      $isWeeken = UserHelper::isWeeken($date);
      $isNPKL = UserHelper::isNPKL($id, $date);
      $isNPCL = UserHelper::isNPCL($id, $date);
      if ($isHoliday || $isNPCL) {
        $classes = 'ncl';
      }
      if ($isNPKL) {
        $classes = 'nkl';
      }
      if ($isWeeken) {
        $classes = 'weeken';
      }
      if (date('Y-m-d') >= date('Y-m-d', strtotime($date))) {
        $event = Event::where('user_code', $id)->where('date', $date)->first();
        if ($event == null) {
          array_push($events, [
            'id' => $date,
            'startDate' => date('Y-m-d', strtotime($date)),
            'endDate' => date('Y-m-d', strtotime($date)),
            'title' => '|',
            'classes' => $classes,
            'number_of_fines' => 0,
            'working_day' => 0,
            'fined_time' => 0,
            'overtime' => 0,
            'time_in' => '',
            'time_out' => '',
            'status' => '',
            'type' => null,
            'date' => date('D, d-m-Y', strtotime($date)),
          ]);
        } else {
          if ($classes == 'ktc') {
            $classes = 'dg';
            if (date('Y-m-d') !== date('Y-m-d', strtotime($event->date))) {
              if ($event->type == null) {
                $classes = 'ktc';
              } else if ($event->status == 1) {
                $classes = 'dmvs';
              } else if ($event->status == 2) {
                $classes = 'dlb';
              }
            }
          }


          $working_day = 0;
          $fined_time = 0;
          $number_of_fines = 0;
          if (!$isWeeken && !$isHoliday && !$isNPCL && !$isNPKL) {
            if ($event->type == 1 || $event->type == 2) {
              $working_day = 0.5;
            } else if ($event->type == 3) {
              $working_day = 1;
            }
            $fined_time = $event->fined_time;
            $number_of_fines = $event->ILM + $event->LEM + $event->ILA + $event->LEA;
          }

          $time_in = date('H:i', strtotime($event->time_in));
          $time_out = $event->time_out ? date('H:i', strtotime($event->time_out)) : '--:--';
          $overtime = 0;
          if ($event->form_requests) {
            foreach ($event->form_requests as $form_request) {
              if ($form_request->type == 'OT' && $form_request->has_worked == 1) {
                $overtime = $form_request->range_time;
              }
            }
          }
          array_push($events,  [
            'id' => $date,
            'startDate' => date('Y-m-d', strtotime($event->date)),
            'endDate' => date('Y-m-d', strtotime($event->date)),
            'title' => $time_in . ' | ' . $time_out,
            'classes' => $classes,
            'fined_time' => $fined_time,
            'number_of_fines' =>  $number_of_fines,
            'working_day' => $working_day,
            'overtime' => $overtime,
            'time_in' => date('H:i', strtotime($event->time_in)),
            'time_out' => $event->time_out ? date('H:i', strtotime($event->time_out)) : '--',
            'status' => $event->status,
            'type' => $event->type,
            'date' => date('D, d-m-Y', strtotime($date)),
          ]);
        }
      }
    }
    // dd($events);
    // return [
    //   'id' => $this->id,
    //   'startDate' => date('Y-m-d', strtotime($this->date)),
    //   'endDate' => date('Y-m-d', strtotime($this->date)),
    //   'title' => $time_in . ' | ' . $time_out,
    //   'classes' => $classes,
    //   // 'url' => $url
    //   'fined_time' => $this->fined_time,
    //   'number_of_fines' =>  $this->ILM + $this->LEM + $this->ILA + $this->LEA,
    //   'working_day' => $working_day,
    //   'form_requests' => $this->form_requests,
    // ];
    // $events = Event::where('user_code', $id)->where('date', 'like', $date . '%')->with('form_requests')->get();
    return response(['data' => $events]);
  }

  public function store(Request $request)
  {
    //
  }

  public function update(Request $request)
  {
    if ($request->user()->can('update-timesheets')) {
      $event = Event::where('user_code', $request->user_code)->where('date', date('Y-m-d', strtotime($request->date)))->first();
      if ($event == null) {
        $new_event = new Event();
        $new_event->date = date('Y-m-d', strtotime($request->date));
        $new_event->time_in = date('H:i', strtotime($request->time_in));
        $new_event->time_out = date('H:i', strtotime($request->time_out));
        $new_event->user_code = $request->user_code;
        $new_event->status = 1;
        $ev_update = EventHelper::updateEventInfo($new_event);
        $ev_update->save();
      } else {
        $event->time_in = date('H:i', strtotime($request->time_in));
        $event->time_out = date('H:i', strtotime($request->time_out));
        $event = EventHelper::updateEventInfo($event);
        $event->save();
      }
      return response([
        'status' => true,
        'message' => 'Cập nhật thành công!'
      ], 200);
    }
    return response([
      'status' => false,
      'message' => 'Người dùng này không có quyền chỉnh sửa timesheets của nhân viên!'
    ], 200);
  }

  public function dailyCheckInOut(Request $request)
  {
    $user = Auth::user();
    if ($user->can('update-timesheets')) {
      $employees = EventHelper::dailyEvent($request);
      return $employees;
    };
    return response([
      'status' => false,
      'message' => 'Người dùng này không có quyền xem bảng chấm công của nhân viên!'
    ], 200);
  }
}
