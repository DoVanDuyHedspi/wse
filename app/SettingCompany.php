<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettingCompany extends Model
{
  protected $fillable = ['type', 'value'];

  protected $casts = [
    'value' => 'array',
  ];
}
