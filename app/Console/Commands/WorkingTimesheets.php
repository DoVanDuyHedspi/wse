<?php

namespace App\Console\Commands;

use App\Event;
use App\FormRequest;
use App\Helpers\EventHelper;
use App\Lib\WorkLib;
use App\SettingCompany;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
    Log::info("Running!");
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
          $new_event->addMediaFromBase64($event["SnapPicinfo"])->toMediaCollection('check_in');
        } else if ($ev->time_out == null) {
          $image_collection = "";
          if (date('H:i', strtotime($ev->time_in)) > $time) {
            $ev->time_out = $ev->time_in;
            $ev->time_in = $time;
            $image_collection = "check_in";
          } else {
            $ev->time_out = $time;
            $image_collection = "check_out";
          }
          $ev_update = EventHelper::updateEventInfo($ev);
          $ev_update->save();
          $ev->clearMediaCollection($image_collection);
          $ev->addMediaFromBase64($event["SnapPicinfo"])->toMediaCollection($image_collection);
          self::updateFormRequest($ev_update);
        } else if (date('H:i', strtotime($ev->time_out)) <= $time) {
          $ev->time_out = $time;
          $ev_update = EventHelper::updateEventInfo($ev);
          $ev_update->save();
          $ev->clearMediaCollection('check_out');
          $ev->addMediaFromBase64($event["SnapPicinfo"])->toMediaCollection('check_out');
          self::updateFormRequest($ev_update);
        }
      }
    }
    self::fetchDataInfo();
    $this->info('Thành công!');
  }

  static function updateFormRequest($event_work)
  {
    $form_requests = FormRequest::whereDate('work_date', '=', date('Y-m-d', strtotime($event_work->date)))
      ->whereIn('type', ['ILM', 'ILA', 'LO', 'LEM', 'LEA'])->where('status', 'accept')->get();
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

  static function fetchDataInfo()
  {
    $value = [];
    $timekeeping_data = SettingCompany::where('type', 'fetch_data_info')->first();
    if (!$timekeeping_data) {
      $timekeeping_data = new SettingCompany();
      $timekeeping_data->type = 'fetch_data_info'; 
      $value['fetch_at'] = date('d-m-Y H:i:s');
      $timekeeping_data->value = $value;
    }
    $value = $timekeeping_data->value;
    $value['fetch_at'] = date('d-m-Y H:i:s');
    $timekeeping_data->value = $value;
    $timekeeping_data->save();
  }
}
