<?php

namespace App\Http\Resources;

use App\Group;
use Illuminate\Http\Resources\Json\JsonResource;

class FormComplainResource extends JsonResource
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
      'user_code' => $this->user_code,
      'name' => $this->user->name,
      'user_id' => $this->user->id,
      'begin_time' => $this->begin_time,
      'end_time' => $this->end_time,
      'date' => date('d-m-Y', strtotime($this->date)),
      'user_avatar' => $this->user->getFirstMediaUrl('avatar'),
      'note' => $this->note,
      'created_at' => date('d-m-Y H:i:s', strtotime($this->created_at)),
      'group' => $this->user->group->name,
      'branch' => $this->user->branch->name,
      'position' => $this->user->position->name,
      'search_info' => $this->search_info
    ];
  }
}
