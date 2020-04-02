<?php

namespace App\Http\Controllers\Api;

use App\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource as EventResource;

class EventController extends Controller
{
  public function show(Request $request, $id)
  {
    $date = $request->query()['date'];
    $date = date('Y-m', strtotime($date));
    $events = Event::where('user_code', $id)->where('date', 'like', $date.'%')->get();  
    return EventResource::collection($events);
  }

  public function store(Request $request)
  {
    //
  }
}
