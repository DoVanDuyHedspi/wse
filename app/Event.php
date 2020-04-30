<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  protected $fillable = [
    'date', 'user_code', 'time_in', 'time_out', 'ILM', 'ILA', 'LEM', 'LEA', 'QQD', 'QQV'
  ];

  protected $casts = [
    'date' => 'date:Y-m-d',
  ];

  public function form_requests() 
  {
    return $this->hasMany(FormRequest::class);
  }
}
