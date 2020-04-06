<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormRequest extends Model
{
  public function user()
  {
    return $this->belongsTo(User::class, 'user_code', 'employee_code');
  }

  protected $casts = [
    'work_date' => 'date:d-m-Y',
    'leave_date' => 'date:d-m-Y',
    'created_at' => 'datetime:d-m-Y H:i:s'
  ];
}
