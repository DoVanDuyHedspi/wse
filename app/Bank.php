<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
  protected $fillable = ['type', 'account_number', 'name', 'user_id'];
}
