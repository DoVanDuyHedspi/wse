<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
  protected $fillable = ['type', 'brand', 'license_plates', 'user_id'];
}
