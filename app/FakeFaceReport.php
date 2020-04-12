<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class FakeFaceReport extends Model implements HasMedia
{
  use Notifiable,  HasMediaTrait;
  protected $fillable = ['branch_id', 'time'];
}
