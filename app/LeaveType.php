<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
  protected $fillable = ['name', 'slug', 'description', 'numbers_day', 'has_salary',
    'has_accumulated', 'expiry_date', 'type'];
  
    protected $casts = [
      'expiry_date' => 'date:d-m-Y',
    ];
}
