<?php

namespace App\Http\Controllers\Api;

use App\Branch;
use App\FakeFaceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Lib\WorkLib;

class FakeFaceReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(Request $request)
  {
    $filter = $request->query();
    if (count($filter)) {
      $reports = FakeFaceReport::when($filter['branch_id'], function ($query, $branch_id) {
        return $query->where('branch_id', $branch_id);
      })->when($filter["date_begin"], function ($query, $date_begin) {
        return $query->whereDate("date_time", '>=', date('Y-m-d H:i:s', strtotime($date_begin)));
      })->when($filter["date_end"], function ($query, $date_end) {
        return $query->whereDate("date_time", '<=', date('Y-m-d H:i:s', strtotime($date_end)));
      })->with('user')->orderBy('created_at', 'desc')->get();
    } else {
      $reports = FakeFaceReport::orderBy('created_at', 'desc')->with('user')->get();
    }


    foreach ($reports as $report) {
      $report['image'] = $report->getFirstMediaUrl('fake_face');
      $report->date_time = date('d-m-Y H:i:s', strtotime($report->date_time));
      $branch = Branch::find($report->branch_id);
      $report['branch'] = $branch->name;
    }
    return response([
      'status' => true,
      'reports' => $reports,
    ]);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'date_time' => 'required|date',
      'branch_id' => 'required',
      'image_base64' => 'required'
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Hãy nhập đủ thông tin!'
      ], 200);
    }
    $report = new FakeFaceReport();
    $data = new WorkLib();
    $res = $data->searchUserByPicture($request->image_base64);
    
    if (isset($res['info']['SearchInfo'])) {
      $user_code = $res['info']['SearchInfo'][0]['CustomizeID'];
      $report->user_code = $user_code;
      $date = date('Y-m-d', strtotime($request->date_time));
      $end_time = date('H:i:s', strtotime($request->date_time));
      $begin_time = date('H:i:s', (strtotime($request->date_time)-300));
      $result = $data->searchEventOfUser($date, $begin_time, $end_time, $user_code);
      if (isset($result['info']['SearchInfo'])) {
        $report->spoofing_success = 1;
      }
    }
    $report->branch_id = $request->branch_id;
    $report->date_time = date('Y-m-d H:i:s', strtotime($request->date_time));
    if ($report->save()) {
      $report->addMediaFromBase64($request->image_base64)->toMediaCollection('fake_face');
    };
    return response([
      'status' => true,
      'report' => $report
    ]);
  }

  public function show(FakeFaceReport $fakeFaceReport)
  {
    //
  }

  public function edit(FakeFaceReport $fakeFaceReport)
  {
    //
  }

  public function update(Request $request, FakeFaceReport $fakeFaceReport)
  {
    //
  }

  public function destroy(Request $request, FakeFaceReport $fakeFaceReport)
  {
    if ($fakeFaceReport) {
      $fakeFaceReport->clearMediaCollection('fake_face');
      $fakeFaceReport->delete();
      return response(['status' => true]);
    }
    return response(['status' => false, 'message' => 'Report này không tồn tại!']);
  }
}
