<?php

namespace App\Http\Controllers\Api;

use App\FormComplain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Components\GoogleClient;
use App\Event;
use App\Helpers\EventHelper;
use App\Http\Resources\FormComplainResource;
use App\Lib\WorkLib;
use App\Notifications\ResultOfComplain;
use App\Notifications\ResultOfRequest;
use Exception;
use Google_Service_Drive;
use Illuminate\Support\Str;

class FormComplainController extends Controller
{
  protected $client;

  public function __construct(GoogleClient $client)
  {
    $this->middleware('auth:api');
    $this->client = $client->getClient();
  }

  public function index(Request $request)
  {
    $filter = $request->query();
    if (count($filter)) {
      $forms = Auth::user()->form_complains()->when($filter["status"], function ($query, $status) {
        return $query->where("status", $status);
      })->when($filter["month"], function ($query, $month) {
        return $query->where("created_at", "like", '%' . date('Y-m', strtotime($month)) . '%');
      })->with('user')->orderBy('created_at', 'desc')->get();
    } else {
      $forms = Auth::user()->form_complains()->with('user')->orderBy('created_at', 'desc')->get();
    }

    return $forms;
  }

  public function usersRequests(Request $request)
  {
    if (Auth::user()->can('check-requests') || Auth::user()->can('approve-requests')) {
      $filter = $request->query();
      if (count($filter)) {
        $forms = FormComplain::with('user')->when($filter["status"], function ($query, $status) {
          return $query->where("status", $status);
        })->when($filter["date_begin"], function ($query, $date_begin) {
          return $query->whereDate("created_at", '>=', date('Y-m-d H:i:s', strtotime($date_begin)));
        })->when($filter["date_end"], function ($query, $date_end) {
          return $query->whereDate("created_at", '<=', date('Y-m-d H:i:s', strtotime($date_end)));
        })->whereHas('user', function ($query) use ($filter) {
          return $query->when($filter["group_id"], function ($q, $group_id) {
            return $q->where("group_id", $group_id);
          })->when($filter["branch_id"], function ($q, $branch_id) {
            return $q->where("branch_id", $branch_id);
          })->when($filter["search"], function ($q, $search) {
            return $q->where(function ($qr) use ($search) {
              $qr->where("name", "like", '%' . $search . '%')->orWhere("employee_code", "like", '%' . $search . '%');
            });
          });
        })->orderBy('created_at', 'desc')->get();
      } else {
        $forms = FormComplain::with('user')->orderBy('created_at', 'desc')->get();
      }
      return $forms;
    }
    return response(['status' => false, 'message' => 'Bạn không có quyền kiểm tra hoặc duyệt yêu cầu của nhân viên'], 200);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'date' => 'required | date',
      'begin_time' => 'required | regex:/(\d+\:\d+)/',
      'end_time' => 'required | regex:/(\d+\:\d+)/',
      'note' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ và đúng thông tin!'
      ], 200);
    }
    //kiem tra xem co event trong khoang thoi gian nay chua
    $message = '';
    $begin_time =  date('H:i', strtotime($request->begin_time));
    $end_time = date('H:i', strtotime($request->end_time));
    $event = Event::where('user_code', $request->user()->employee_code)->whereDate('date', date('Y-m-d', strtotime($request->date)))->first();
    if ($event) {
      $time_in = date('H:i', strtotime($event->time_in));
      if ($begin_time <= $time_in && $time_in <= $end_time) {
        $message = 'Đã có bản ghi chấm công trong khoảng thời gian xác minh!';
      }
      if ($event->time_out) {
        $time_out = date('H:i', strtotime($event->time_out));
        if ($begin_time <= $time_out && $time_out <= $end_time) {
          $message = 'Đã có bản ghi chấm công trong khoảng thời gian xác minh!';
        }
        if ($time_in <= $begin_time && $end_time <= $time_out) {
          $message = 'Thời điểm xác minh đã nằm trong thời gian được chấm công!';
        }
      }
    }
    if ($end_time <= $begin_time) {
      $message = 'Thời điểm kết thúc phải sau thời điểm bắt đầu';
    }
    if (strlen($message) != 0) {
      return response([
        'status' => false,
        'message' => $message
      ], 200);
    }
    $form = new FormComplain();
    $form->begin_time = $begin_time;
    $form->end_time = $end_time;
    $form->user_code = $request->user()->employee_code;
    $form->date = date('Y-m-d', strtotime($request->date));
    $form->note = $request->note;
    $form->status = 'waiting';
    $form->result = 'waiting';
    $form->save();
    return response(['status' => true]);
  }

  public function show(FormComplain $formComplain)
  {
    if ($formComplain->user_code != Auth::user()->employee_code) {
      return response([
        'status' => false,
        'message' => "Bạn không có quyền chỉnh sửa yêu cầu của người này!"
      ], 200);
    }
    $formComplain['user_group'] = $formComplain->user->group_id;
    $formComplain['user_name'] = $formComplain->user->name;
    $formComplain['user_branch'] = $formComplain->user->branch_id;
    return $formComplain;
  }

  public function update(Request $request, FormComplain $formComplain)
  {
    if ($formComplain->user_code != Auth::user()->employee_code) {
      return response([
        'status' => false,
        'message' => "Bạn không có quyền chỉnh sửa yêu cầu của người này!"
      ], 200);
    }
    $validator = Validator::make($request->all(), [
      'date' => 'required | date',
      'begin_time' => 'required | regex:/(\d+\:\d+)/',
      'end_time' => 'required | regex:/(\d+\:\d+)/',
      'note' => 'required',
      'id' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ thông tin!'
      ], 200);
    }
    $form = FormComplain::where('id', $request->id)->first();
    if ($form == null) {
      return response([
        'status' => false,
        'message' => 'Yêu cầu này không tồn tại!'
      ], 200);
    }
    $form->begin_time = date('H:i', strtotime($request->begin_time));
    $form->end_time = date('H:i', strtotime($request->end_time));
    if ($form->end_time <= $form->begin_time) {
      return response([
        'status' => false,
        'message' => 'Thời điểm kết thúc phải sau thời điểm bắt đầu'
      ], 200);
    }
    $form->date = date('Y-m-d', strtotime($request->date));
    $form->note = $request->note;
    $form->save();
    return response([
      'status' => true
    ], 200);
  }

  public function destroy(FormComplain $formComplain)
  {
    if ($formComplain->user_code != Auth::user()->employee_code) {
      return response([
        'status' => false,
        'message' => "Bạn không có quyền xóa yêu cầu của người này!"
      ], 200);
    }
    $formComplain->delete();
    return response([
      'status' => true
    ], 200);
  }

  public function approveRequest(Request $request)
  {
    // dd($request);
    $validator = Validator::make($request->all(), [
      'action' => 'required',
      'request_id' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ thông tin!'
      ], 200);
    }
    $form = FormComplain::find($request->request_id);
    if ($form == null) {
      return response([
        'status' => false,
        'message' => 'Yêu cầu này không tồn tại'
      ], 200);
    }
    if ($request->action == 'refuse' || $request->action == 'accept') {
      $form->status = $request->action;
      if ($form->status == 'refuse') {
        $form->result = 'fail';
      }
    } else {
      $form->result = $request->action;
      if ($form->result == 'success') {
        if ($request->time == null) {
          return response([
            'status' => false,
            'message' => 'Hãy nhập thời gian chấm công!'
          ], 200);
        } else {
          $form->reply = $request->reply;
          $time = date('H:i', strtotime($request->time));
          $date = date("Y-m-d", strtotime($form->date));
          $ev = Event::where('date', $date)->where('user_code', $form->user_code)->first();
          if (!$ev) {
            $new_event = new Event();
            $new_event->date = $date;
            $new_event->time_in = $time;
            $new_event->user_code = $form->user_code;
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
          } else if (date('H:i', strtotime($ev->time_out)) <= $time) {
            $ev->time_out = $time;
            $ev_update = EventHelper::updateEventInfo($ev);
            $ev_update->save();
          }
        }
      }
    }
    $form->user->notify(new ResultOfComplain($form));
    $form->save();
    return response(['status' => true, 'form_complain' => $form], 200);
  }

  public function approvedForm(Request $request)
  {
    // $validator = Validator::make($request->all(), [
    //   'date' => 'required | date',
    // ]);
    // if ($validator->fails()) {
    //   return response([
    //     'status' => false,
    //     'message' => 'Thiếu ngày cần xác minh!'
    //   ], 200);
    // }
    //get video id
    // $date = date('Y-m-d', strtotime($request->date));
    // $videoName = 'wse-' . $date;
    // $drive = new Google_Service_Drive($this->client);
    // $query   =   "'" . config('wse.drive_folder_id') . "' in parents and trashed=false";
    // $optParams   =   [
    //   'fields' => 'files(id, name)',
    //   'q'   =>   $query
    // ];
    // $results   =   $drive->files->listFiles($optParams);
    // $listVideo = (array) $results->files;
    // $listVideoUrl = [];
    // foreach ($listVideo as $video) {
    //   if ($video->name == $videoName) {
    //     $url = "https://drive.google.com/file/d/" . $video->id . "/preview";
    //     array_push($listVideoUrl, $url);
    //   }
    // }
    //get request and user info
    try {
      $forms = FormComplain::with('user')->where('status', 'accept')->where('result', 'waiting')->orderBy('created_at', 'desc')->get();
      foreach ($forms as $form) {
        $data = new WorkLib();
        $res = $data->searchEventOfUser($form->date, $form->begin_time, $form->end_time, $form->user_code);
        // dd($res);
        $search_info = [];
        if (isset($res['info']['SearchInfo'])) {
          $events = $res['info']['SearchInfo'];
          foreach ($events as $event) {
            array_push($search_info, date('H:i:s', strtotime($event['Time'])));
          }
        }
        $form['search_info'] = $search_info;
      }
      $forms = FormComplainResource::collection($forms);
      return response(['forms' => $forms]);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }

    // return response(['forms' => $forms, 'list_video_url' => array_reverse($listVideoUrl)]);
  }

  public function getVideoFromGgDrive(Request $request)
  {
    try {
      $params = $request->query();

      if (!$params['date'] || !$params['begin_time'] || !$params['end_time']) {
        return response([
          'status' => false,
          'message' => 'Hãy nhập đủ thông tin ngày và khoảng thời gian muốn lấy video',
        ], 200);
      } else {
        $datetime_begin = strtotime($params['date'] . ' ' . $params['begin_time']);
        $datetime_end = strtotime($params['date'] . ' ' . $params['end_time']);
        $drive = new Google_Service_Drive($this->client);
        $query   =   "'" . config('wse.drive_folder_id') . "' in parents and trashed=false";
        $optParams   =   [
          'fields' => 'files(id, name)',
          'q'   =>   $query
        ];

        $results   =   $drive->files->listFiles($optParams);
        $listFiles = (array) $results->files;
        $listVideoUrl = [];
        foreach ($listFiles as $file) {
          $res = Str::is('*-*.*', $file->name);
          if ($res) {
            $nameFile = explode(".", $file->name)[0];
            $video_begin = explode("-", $nameFile)[0];
            $video_end = explode("-", $nameFile)[1];
            if ($datetime_begin > $video_end || $datetime_end < $video_begin) {
              continue;
            } else {
              array_push($listVideoUrl, "https://drive.google.com/file/d/" . $file->id . "/preview");
            }
          }
        }
        return array_reverse($listVideoUrl);
      }
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }
}
