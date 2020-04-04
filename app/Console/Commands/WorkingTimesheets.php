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
    // dd(Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d\\T H:i:s'));
    if (isset($res['info']['SearchInfo'])) {
      $events = $res['info']['SearchInfo'];
      $events = array_reverse($events);
      foreach ($events as $event) {
        $date = date("Y-m-d", strtotime($event["Time"]));
        $time = date("H:i", strtotime($event["Time"]));
        $ev = Event::where('date', $date)->first();
        if (!$ev) {
          $new_event = new Event();
          $new_event->date = $date;
          $new_event->time_in = $time;
          $new_event->user_code = $event['CustomizeID'];
          $new_event->status = 1;
          $new_event->save();
        } else if(date('H:i', strtotime($ev->time_out))<=$time)  {         
          $ev->time_out = $time;
          $ev_update = EventHelper::updateEventInfo($ev);
          $ev_update->save();
        }
      }
    }

    $this->info('Thành công!');
  }
}
