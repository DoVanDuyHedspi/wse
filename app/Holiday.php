<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
  protected $fillable = ['name', 'start_date', 'end_date', 'range_date', 'coefficients_salary'];

  protected $casts = [
    'start_date' => 'date:d-m-Y',
    'end_date' => 'date:d-m-Y',
  ];
}
