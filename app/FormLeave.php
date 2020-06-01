<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormLeave extends Model
{
  protected $fillable = [
    'user_code', 'approver_code', 'status',
    'leave_type_id', 'number_days', 'begin_leave_date', 'end_leave_date'
  ];

  protected $casts = [
    'begin_leave_date' => 'date:d-m-Y',
    'end_leave_date' => 'date:d-m-Y',
    'created_at' => 'datetime: d-m-Y H:i:s'
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_code', 'employee_code');
  }

  public function approver()
  {
    return $this->belongsTo(User::class, 'approver_code', 'employee_code');
  }

  public function leave_type()
  {
    return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
  }
}
