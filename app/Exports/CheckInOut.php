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

class CheckInOut implements FromView
{
  use Exportable;
  protected $listUserIds;
  protected $users;
  protected $date;
  public function __construct($listUserIds, $date)
  {
    $this->listUserIds = $listUserIds;
    $this->date = $date;
    $this->users = User::whereIn('id', $this->listUserIds)->get();
    self::getInfo();
  }

  public function view(): View
  {
    return view('report.checkInOut', [
      'users' => $this->users,
      'date' => $this->date
    ]);
  }

  public function getInfo()
  {
    foreach ($this->users as $key => $user) {
      $event = Event::where('date', $this->date)->where('user_code', $user->employee_code)->first();
      if($event) {
        $this->users[$key]['time_in'] = $event->time_in;
        $this->users[$key]['time_out'] = $event->time_out;
        $this->users[$key]['fined_time'] = $event->time_out;
      } else {
        $this->users[$key]['time_in'] = "";
        $this->users[$key]['time_out'] = "";
        $this->users[$key]['fined_time'] = 0;
      }
    }
  }
}
