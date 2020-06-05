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

class Shiftwork implements FromView
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
    self::getInfoShiftwork();
  }

  public function view(): View
  {
    return view('report.shiftwork', [
      'users' => $this->users,
      'daysOfMonth' => $this->allDaysOfMonth,
      'month' => $this->month,
    ]);
  }

  public function getInfoShiftwork()
  {
    $year = date('Y', strtotime($this->month));
    $month = date('m', strtotime($this->month));
    $allDaysOfMonth = UserHelper::getAllDayOfMonth($year, $month);
    $this->allDaysOfMonth = $allDaysOfMonth;
    foreach ($this->users as $key => $user) {
      $total_works = 0;
      $total_faults = 0;
      $total_ot = 0;
      $total_holiday = 0;
      $total_ncl = 0;
      $total_nkl = 0;
      foreach ($allDaysOfMonth as $date) {
        $event = Event::where('user_code', $user->employee_code)->where('date', $date)->first();
        $holiday = Holiday::whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->first();
        if ($holiday) {
          $total_holiday += 1;
        }
        $leave = FormLeave::where('user_code', $user->employee_code)
          ->whereDate('begin_leave_date', '<=', $date)->whereDate('end_leave_date', '>=', $date)->first();
        $isNcl = false;
        if ($leave) {
          if ($leave->leave_type->has_salary) {
            $total_ncl += 1;
            $isNcl = true;
          } else {
            $total_nkl += 1;
          }
        }
        if ($event == null || UserHelper::isWeeken($date) || $isNcl || $holiday) {
          $this->users[$key][$date] = 0;
        } else if ($event->type == 3) {
          $this->users[$key][$date] = 1;
        } else if ($event->type == 1 || $event->type == 2) {
          $this->users[$key][$date] = 0.5;
        } else {
          $this->users[$key][$date] = 0;
        }
        if ($event != null) {
          $total_faults += $event->ILM + $event->ILA + $event->LEM + $event->LEA;
        }
        $total_works += $this->users[$key][$date];
      }
      $total_ot_time = FormRequest::where('user_code', $user->employee_code)->whereIn('work_date', $allDaysOfMonth)->where('type', 'OT')
        ->where('has_worked', 1)->sum('range_time');
      $total_ot = $total_ot_time / (8 * 60);
      $this->users[$key]['total_ot'] = $total_ot;
      //tong so cong lam viec
      $this->users[$key]['total_works'] = $total_works;
      //tong so loi di muon ve som
      $this->users[$key]['total_faults'] = $total_faults;
      //tong so ngay nghi le
      $this->users[$key]['total_holiday'] = $total_holiday;
      //tong ngay nghi co luong
      $this->users[$key]['total_ncl'] = $total_ncl;
      //tong ngay nghi khong luong
      $this->users[$key]['total_nkl'] = $total_nkl;
      //tong cong tinh luong
      $this->users[$key]['total_cl'] = $total_works + $total_ncl + $total_ot + $total_holiday; 

      
    }
  }
}
