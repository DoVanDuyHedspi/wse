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

class TimesheetsEmployee implements FromView
{
  use Exportable;
  protected $user;
  protected $month;
  protected $events;
  protected $daysOfMonth;
  public function __construct($employee_code, $month)
  {
    $this->month = $month;
    $this->user = User::where('employee_code', $employee_code)->first();
    self::getInfo();
  }

  public function view(): View
  {
    
    return view('report.timesheetsEmployee', [
      'user' => $this->user,
      'month' => $this->month,
      'events' => $this->events,
      'daysOfMonth' => $this->daysOfMonth,
    ]);
  }

  public function getInfo()
  {
    
    $year = date('Y', strtotime($this->month));
    $month = date('m', strtotime($this->month));
    $events = [];
    $allDayOfMonth = UserHelper::getAllDayOfMonth($year, $month);
    $this->daysOfMonth = $allDayOfMonth;
    $total_work = 0;
    $total_fined = 0;
    $total_fined_time = 0;
    $total_ot = 0;

    foreach ($allDayOfMonth as $date) {
      $classes = 'ktc';
      $isHoliday = UserHelper::isHoliday($date);
      $isWeeken = UserHelper::isWeeken($date);

      $isNPKL = UserHelper::isNPKL($this->user->employee_code, $date);
      $isNPCL = UserHelper::isNPCL($this->user->employee_code, $date);
      if ($isHoliday || $isNPCL) {
        $classes = 'ncl';
      }
      if ($isNPKL) {
        $classes = 'nkl';
      }
      if ($isWeeken) {
        $classes = 'weeken';
      }

      $event = Event::where('user_code', $this->user->employee_code)->where('date', $date)->first();
      if ($event == null) {
        array_push($events, [
          'classes' => $classes,
          'time_in' => '',
          'time_out' => '',
          'fined_time' => 0,
          'ot_time' => 0,
          'date' => date('d-m-Y', strtotime($date)),
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
        $total_work += $working_day;
        $total_fined += $number_of_fines;
        $total_fined_time += $fined_time;

        $overtime = 0;
        if ($event->form_requests) {
          foreach ($event->form_requests as $form_request) {
            if ($form_request->type == 'OT' && $form_request->has_worked == 1) {
              $overtime = $form_request->range_time;
            }
          }
        }
        $total_ot += $overtime;
        array_push($events,  [
          'classes' => $classes,
          'fined_time' => $fined_time,
          'ot_time' => $overtime,
          'time_in' => date('H:i', strtotime($event->time_in)),
          'time_out' => $event->time_out ? date('H:i', strtotime($event->time_out)) : '',
          'date' => date('d-m-Y', strtotime($date)),
        ]);
      }
    }

    $this->events = $events;
    $this->user['total_work'] = $total_work;
    $this->user['total_fined'] = $total_fined;
    $this->user['total_fined_time'] = $total_fined_time;
    $this->user['total_ot'] = $total_ot/8;
  }
}
