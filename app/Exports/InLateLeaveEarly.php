<?php

namespace App\Exports;

use App\Event;
use App\FormLeave;
use App\FormRequest;
use App\Helpers\UserHelper;
use App\Holiday;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InLateLeaveEarly implements FromView
{
  use Exportable;
  protected $listUserIds;
  protected $users;
  protected $month;
  protected $allDaysOfMonth;
  public function __construct($listUserIds, $month)
  {
    $this->listUserIds = $listUserIds;
    $this->month = $month;
    $this->users = User::whereIn('id', $this->listUserIds)->get();
    self::getInfoFaults();
  }

  public function view(): View
  {
    return view('report.inLateLeaveEarly', [
      'users' => $this->users,
      'daysOfMonth' => $this->allDaysOfMonth
    ]);
  }

  public function getInfoFaults()
  {
    $year = date('Y', strtotime($this->month));
    $month = date('m', strtotime($this->month));
    $allDaysOfMonth = UserHelper::getAllDayOfMonth($year, $month);
    $this->allDaysOfMonth = $allDaysOfMonth;
    foreach ($this->users as $key => $user) {
      $total_il = 0;
      $total_le = 0;
      $total_time = 0;
      foreach ($allDaysOfMonth as $date) {
        $event = Event::where('user_code', $user->employee_code)->where('date', $date)->first();
        $holiday = Holiday::whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->first();
        $leave = FormLeave::where('user_code', $user->employee_code)
          ->whereDate('begin_leave_date', '<=', $date)->whereDate('end_leave_date', '>=', $date)->first();
        if($event && !$holiday && !$leave && !UserHelper::isWeeken($date)) {
          $total_il += $event->ILM + $event->ILA;
          $total_le += $event->LEM + $event->LEA;
          $attr = [];
          $attr['date'] = $date;
          $attr['IL'] = ($event->ILM + $event->ILA) ? 1 : 0;
          $attr['LE'] = ($event->LEM + $event->LEA) ? 1 : 0;
          $attr['time'] = $event->fined_time;
          $this->users[$key][$date] = $attr;
          $total_time += $event->fined_time;
        } else {
          $attr = [];
          $attr['date'] = $date;
          $attr['IL'] = 0;
          $attr['LE'] = 0;
          $attr['time'] = 0;
          $this->users[$key][$date] = $attr;
        }
      }
      
      $this->users[$key]['total_il'] = $total_il;
      $this->users[$key]['total_le'] = $total_le;
      $this->users[$key]['total_faults'] = $total_le + $total_il;
      $this->users[$key]['total_time'] = $total_time;
    }
  }
}
