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
      $allDayOfMonth = UserHelper::getAllDayOfMonth($year, $month);
      // $employees = User::where('group_id', $user->group_id)->get();
      $filter = $request->query();
      if (count($filter)) {
        $employees = User::getRelationships()->filter($filter)->get();
      } else {
        $employees = User::all();
      };
      
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
      'message' => 'Người dùng này không có quyền xem timesheets của nhân viên!'
    ], 200);
  }

  public function show(Request $request, $id)
  {
    $date = $request->query()['date'];
    $date = date('Y-m', strtotime($date));
    $events = Event::where('user_code', $id)->where('date', 'like', $date . '%')->get();
    return EventResource::collection($events);
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
        $new_event->save();
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
      'message' => 'Người dùng này không có quyền xem timesheets của nhân viên!'
    ], 200);
  }
}
