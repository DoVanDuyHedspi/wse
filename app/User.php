<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class User extends Authenticatable implements HasMedia
{
  use Notifiable, HasPermissionsTrait, HasMediaTrait;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password', 'phone_number', 'nationality', 'gender',
    'birthday', 'official_start_day', 'position_id', 'tax_code', 'employee_type_id',
    'branch_id', 'group_id', 'personal_email', 'birthplace', 'current_address',
    'permanent_address', 'salary_rank_id'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'birthday' => 'date:d-m-Y',
    'official_start_day' => 'date:d-m-Y'
  ];

  public function scopeGetRelationships($query)
  {
    return $query->with('position')
      ->with('branch')
      ->with('group')
      ->with('slary_rank')
      ->with('bank')
      ->with('education')
      ->with('vehicle')
      ->with('identity_card_passport')
      ->with('employee_type')
      ->with('permissions')
      ->with('roles');
  }

  public function scopeFilter($query, $filter)
  {
    return $query->when($filter["group_id"], function ($query, $group_id) {
      return $query->where("group_id", $group_id);
    })->when($filter["branch_id"], function ($query, $branch_id) {
      return $query->where("branch_id", $branch_id);
    })->when($filter["position_id"], function ($query, $position_id) {
      return $query->where("position_id", $position_id);
    })->when($filter["employee_type_id"], function ($query, $employee_type_id) {
      return $query->where("employee_type_id", $employee_type_id);
    })->when($filter["search"], function ($query, $search) {
      return $query->where(function ($query) use($search) {
        $query->where("name", "like", '%' . $search . '%')->orWhere("email", "like", '%' . $search . '%')->orWhere("employee_code", "like", '%' . $search . '%');
      });
    });
  }

  public function bindAttrsToUser($request)
  {
    $this->name = $request->name;
    $this->email = $request->email;
    $this->phone_number = $request->phone_number;
    $this->birthday = $request->birthday ? date('Y-m-d', strtotime($request->birthday)) : null;
    $this->nationality = $request->nationality;
    $this->gender = $request->gender ? $request->gender : config('wse.gender')[0];
    $this->official_start_day = date('Y-m-d', strtotime($request->official_start_day));
    $this->position_id = $request->position_id;
    $this->group_id = $request->group_id;
    $this->branch_id = $request->branch_id;
    $this->employee_type_id = $request->employee_type_id;
    $this->tax_code = $request->tax_code;
    $this->personal_email = $request->personal_email;
    $this->current_address = $request->current_address;
    $this->permanent_address = $request->permanent_address;
    // $this->salary_rank_id = $request->salary_rank_id;
  }

  public function position()
  {
    return $this->belongsTo(Position::class);
  }

  public function branch()
  {
    return $this->belongsTo(Branch::class);
  }

  public function group()
  {
    return $this->belongsTo(Group::class);
  }

  public function slary_rank()
  {
    return $this->belongsTo(SalaryRank::class);
  }

  public function employee_type()
  {
    return $this->belongsTo(EmployeeType::class);
  }

  public function bank()
  {
    return $this->hasOne(Bank::class);
  }

  public function education()
  {
    return $this->hasOne(Education::class);
  }

  public function identity_card_passport()
  {
    return $this->hasOne(IdentityCardPassport::class);
  }

  public function vehicle()
  {
    return $this->hasOne(Vehicle::class);
  }

  public function events() 
  {
    return $this->hasMany(Event::class, 'user_code', 'employee_code');
  }
}
