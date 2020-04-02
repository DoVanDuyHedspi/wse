<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource as EventResource;
use App\User;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
  public function index()
  {
    return Auth::user();
    $year = date('Y');
    $month = date('m');
    $allDayOfMonth = UserHelper::getAllDayOfMonth($year, $month);
    $group_id = Auth::user()->group_id;
    $user = User::where('group_id', $group_id);
  }

  public function show(Request $request, $id)
  {
    $date = $request->query()['date'];
    $date = date('Y-m', strtotime($date));
    $events = Event::where('user_code', $id)->where('date', 'like', $date . '%')->get();
    return EventResource::collection($events);
  }

  public function store(Request $request)
  {
    //
  }
}
