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
    ->with('employee_type');
  }

  public function bindAttrsToUser($request) {
    $this->name = $request->name;
    $this->employee_code = $request->employee_code;
    $this->email = $request->email;
    $this->phone_number = $request->phone_number;
    $this->birthday = date('Y-m-d',strtotime($request->birthday));
    $this->nationality = $request->nationality;
    $this->gender = $request->gender;
    $this->official_start_day = date('Y-m-d',strtotime($request->official_start_day));
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
}
