<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
        'official_start_day' => 'date:d-m-Y',
        'identity_card_passport' => 'array',
        'vehicles' => 'array',
        'bank' => 'array',
        'education' => 'array',
    ];

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
}