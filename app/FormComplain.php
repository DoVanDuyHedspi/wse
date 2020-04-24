<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormComplain extends Model
{
  protected $fillable = ['user_code', 'begin_time', 'end_time', 'date', 'note', 'reply', 'status', 'result'];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_code', 'employee_code');
  }

  protected $casts = [
    'date' => 'date:d-m-Y',
    'created_at' => 'datetime:d-m-Y H:i:s'
  ];
}
