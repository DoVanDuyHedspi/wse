<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    $classe = 'default';
    // $url = '';
    if (date('Y-m-d') !== date('Y-m-d', strtotime($this->date))) {
      if ($this->type == null) {
        $classe = 'green';
      } else if ($this->status == 1) {
        $classe = 'red';
      } else if ($this->status == 2) {
        $classe = 'pink';
      }
    }

    $working_day = 0;
    if ($this->type == 1 || $this->type == 2) {
      $working_day = 0.5;
    } else if ($this->type == 3) {
      $working_day = 1;
    }
    $time_in = date('H:i', strtotime($this->time_in));
    $time_out = $this->time_out ? date('H:i', strtotime($this->time_out)) : '--:--';
    return [
      'id' => $this->id,
      'startDate' => date('Y-m-d', strtotime($this->date)),
      'endDate' => date('Y-m-d', strtotime($this->date)),
      'title' => $time_in . ' | ' . $time_out,
      'classes' => $classe,
      // 'url' => $url
      'fined_time' => $this->fined_time,
      'number_of_fines' =>  $this->ILM + $this->LEM + $this->ILA + $this->LEA,
      'working_day' => $working_day,
      'form_requests' => $this->form_requests,
    ];
  }
}
