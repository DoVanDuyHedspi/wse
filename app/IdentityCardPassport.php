<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdentityCardPassport extends Model
{
  protected $fillable = ['type', 'code', 'efective_date', 'issued_by', 'user_id'];

  protected $casts = [
    'efective_date' => 'date:d-m-Y',
    'expiry_date' => 'date:d-m-Y'
  ];
}
