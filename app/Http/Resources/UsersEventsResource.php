<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersEventsResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'employee_code' => $this->employee_code,
      'events' => $this->events,
      'number_penalty'=> $this->ILM + $this->LEM + $this->ILA + $this->LEA,
      'ILM' => $this->ILM,
      'ILA' => $this->ILA,
      'LEM' => $this->LEM,
      'LEA' => $this->LEA,
      'number_working_days' => $this->number_working_days,
    ];
  }
}
