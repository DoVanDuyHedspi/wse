<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
  public function parents()
  {
    return $this->belongsTo(self::class, 'parent_id');
  }

  public function children()
  {
    return $this->hasMany(self::class, 'parent_id')->with('children');
  }
}
