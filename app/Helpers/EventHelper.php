<?php

namespace App\Helpers;

use App\Event;
use App\SettingCompany;
use App\User;
use DateTime;

class EventHelper
{
  public static function updateEventInfo($event)
  {
    $setting_timekeeping = SettingCompany::where('type', 'timekeeping')->first();
    $timekeeping = $setting_timekeeping->value;
    $morning_begin = date('H:i', strtotime($timekeeping['morning_begin']));
    $morning_late = date('H:i', strtotime($timekeeping['morning_late']));
    $morning_end = date('H:i', strtotime($timekeeping['morning_end']));
    $afternoon_begin = date('H:i', strtotime($timekeeping['afternoon_begin']));
    $afternoon_late = date('H:i', strtotime($timekeeping['afternoon_late']));
    $afternoon_end = date('H:i', strtotime($timekeeping['afternoon_end']));
    $time_in = date('H:i', strtotime($event->time_in));
    $time_out = date('H:i', strtotime($event->time_out));
    if ($time_in <= $morning_begin) {
      $event->ILM = 0;
      $event->ILA = 0;
      if (($morning_late < $time_out) && ($time_out < $morning_end)) {
        $event->LEM = 1;
        $event->type = 1;
        $event->status = 1;
        $event->fined_time = self::diffTime($morning_end, $time_out);
      } else if (($morning_end <= $time_out) && ($time_out < $afternoon_late)) {
        $event->status = 0; // 1: co loi, 0: khong co loi
        $event->type = 1; //lam sang 2: lam chieu 3: lam full
        $event->LEM = 0;
        $event->fined_time = 0;
      } else if (($afternoon_late <= $time_out) && ($time_out < $afternoon_end)) {
        $event->status = 1;
        $event->LEM = 0;
        $event->LEA = 1;
        $event->type = 3;
        $event->fined_time = self::diffTime($afternoon_end, $time_out);
      } else if ($afternoon_end <= $time_out) {
        $event->status = 0;
        $event->LEA = 0;
        $event->LEM = 0;
        $event->type = 3;
        $event->fined_time = 0;
      }
    } else if (($morning_begin < $time_in) && ($time_in <= $morning_late)) {
      $event->ILA = 0;
      if (($morning_late < $time_out) && ($time_out < $morning_end)) {
        $event->LEM = 1;
        $event->ILM = 1;
        $event->type = 1;
        $event->status = 1;
        $event->fined_time = self::diffTime($time_in, $morning_begin) + self::diffTime($morning_end, $time_out);
      } else if (($morning_end <= $time_out) && ($time_out < $afternoon_late)) {
        $event->status = 1; // 1: co loi, 0: khong co loi
        $event->type = 1; //lam sang 2: lam chieu 3: lam full
        $event->ILM = 1;
        $event->LEM = 0;
        $event->fined_time = self::diffTime($time_in, $morning_begin);
      } else if (($afternoon_late <= $time_out) && ($time_out < $afternoon_end)) {
        $event->status = 1;
        $event->type = 3;
        $event->ILM = 1;
        $event->LEM = 0;
        $event->LEA = 1;
        $event->fined_time = self::diffTime($time_in, $morning_begin) + self::diffTime($afternoon_end, $time_out);
      } else if ($afternoon_end <= $time_out) {
        $event->status = 1;
        $event->ILM = 1;
        $event->type = 3;
        $event->LEA = 0;
        $event->LEM = 0;
        $event->fined_time = self::diffTime($time_in, $morning_begin);
      }
    } else if (($morning_late < $time_in) && ($time_in <= $afternoon_begin)) {
      $event->ILA = 0;
      $event->ILM = 0;
      $event->LEM = 0;
      if (($afternoon_late <= $time_out) && ($time_out < $afternoon_end)) {
        $event->status = 1;
        $event->type = 2;
        $event->LEA = 1;
        $event->fined_time = self::diffTime($afternoon_end, $time_out);
      } else if ($afternoon_end <= $time_out) {
        $event->status = 0;
        $event->type = 2;
        $event->LEA = 0;
        $event->fined_time = 0;
      }
    } else if (($afternoon_begin < $time_in) && ($time_in <= $afternoon_late)) {
      if (($afternoon_late <= $time_out) && ($time_out < $afternoon_end)) {
        $event->status = 1;
        $event->type = 2;
        $event->LEA = 1;
        $event->ILA = 1;
        $event->fined_time = self::diffTime($time_in, $afternoon_begin) + self::diffTime($afternoon_end, $time_out);
      } else if ($afternoon_end <= $time_out) {
        $event->status = 1;
        $event->type = 2;
        $event->LEA = 0;
        $event->ILA = 1;
        $event->fined_time = self::diffTime($time_in, $afternoon_begin);
      }
    }

    return $event;
  }

  public static function diffTime($endTime, $beginTime)
  {
    return (strtotime($endTime) - strtotime($beginTime)) / 60;
  }

  public static function dailyEvent($request)
  {
    $filter = $request->query();
    if (count($filter) && $filter['date']) {
      $date = date('Y-m-d', strtotime($filter['date']));
    } else {
      $date = date('Y-m-d');
    }
    if (count($filter)) {
      $employees = User::getRelationships()->filter($filter)->get();
    } else {
      $employees = User::getRelationships()->get();
    };
    foreach($employees as $employee) {
      $event = Event::where('user_code', $employee->employee_code)->whereDate('date', $date)->first();
      $employee['event'] = $event;
    }
    return $employees;
  }
}
