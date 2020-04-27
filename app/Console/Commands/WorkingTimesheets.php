<?php

namespace App\Console\Commands;

use App\Event;
use App\Helpers\EventHelper;
use App\Lib\WorkLib;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WorkingTimesheets extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'user:timekeeping';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Get user\'s timekeeping';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $data = new WorkLib();
    $res = $data->getTimeKeepingData();
    if (isset($res['info']['SearchInfo'])) {
      $events = $res['info']['SearchInfo'];
      $events = array_reverse($events);
      foreach ($events as $event) {
        $date = date("Y-m-d", strtotime($event["Time"]));
        $time = date("H:i", strtotime($event["Time"]));
        $ev = Event::where('date', $date)->where('user_code', $event['CustomizeID'])->first();
        if (!$ev) {
          $new_event = new Event();
          $new_event->date = $date;
          $new_event->time_in = $time;
          $new_event->user_code = $event['CustomizeID'];
          $new_event->status = 1;
          $new_event->save();
        } else if ($ev->time_out == null) {
          if (date('H:i', strtotime($ev->time_in)) > $time) {
            $ev->time_out = $ev->time_in;
            $ev->time_in = $time;
          } else {
            $ev->time_out = $time;
          }
          $ev_update = EventHelper::updateEventInfo($ev);
          $ev_update->save();
          self::updateFormRequest($ev_update);
        } else if (date('H:i', strtotime($ev->time_out)) <= $time) {
          $ev->time_out = $time;
          $ev_update = EventHelper::updateEventInfo($ev);
          $ev_update->save();
          self::updateFormRequest($ev_update);
        }
      }
    }

    $this->info('Thành công!');
  }

  static function updateFormRequest($event_work)
  {
    $form_requests = Event::whereDate('work_date', '=', date('Y-m-d', strtotime($event_work->date)))->where('status', 'accept')->get();
    if (count($form_requests) != 0) {
      foreach ($form_requests as $form_request) {
        $time_in = date('H:i', strtotime($event_work->time_in));
        $time_out = date('H:i', strtotime($event_work->time_out));
        $work_begin = date('H:i', strtotime($form_request->work_time_begin));
        $work_end = date('H:i', strtotime($form_request->work_time_end));
        $event_leave = Event::whereDate('date', '=', date('Y-m-d', strtotime($form_request->leave_date)))->first();
        if ($time_in <= $work_begin && $time_out >= $work_end) {
          $form_request->has_worked = 1;
          $form_request->save();

          if ($form_request->type == 'ILM') {
            $event_leave->ILM = 0;
          } else if ($form_request->type == 'LEM') {
            $event_leave->LEM = 0;
          } else if ($form_request->type == 'ILA') {
            $event_leave->ILA = 0;
          } else if ($form_request->type == 'LEA') {
            $event_leave->LEA = 0;
          }
          $has_error = $event_leave->ILM + $event_leave->LEM + $event_leave->ILA + $event_leave->LEA;
          if ($has_error == 0) {
            $event_leave->status = 2;
          }
          $event_leave->fined_time -= $form_request->range_time;
          $event_leave->save();
        }
      }
    }
  }
}
