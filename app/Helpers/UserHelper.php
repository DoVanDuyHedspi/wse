<?php

namespace App\Helpers;

use App\Bank;
use App\Education;
use App\FormLeave;
use App\Holiday;
use App\IdentityCardPassport;
use App\Lib\UserLib;
use App\Permission;
use App\Role;
use App\Vehicle;
use Exception;

class UserHelper
{
  public static function createEmployeeCode()
  {
    return rand(10000000,99999999);
    // $lenght = 10;
    // if (function_exists("random_bytes")) {
    //   $bytes = random_bytes(ceil($lenght / 2));
    // } elseif (function_exists("openssl_random_pseudo_bytes")) {
    //   $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    // } else {
    //   throw new Exception("no cryptographically secure random function available");
    // }
    // return substr(bin2hex($bytes), 0, $lenght);
  }

  public static function createInfoExtend($new_info, $user, $checkImage)
  {
    try {
      if ($checkImage) {
        $user->addMediaFromRequest('image')->toMediaCollection('avatar');
      }
      if ($new_info->roles) {
        foreach ($new_info->roles as $roleId) {
          $role = Role::find($roleId);
          $user->roles()->attach($role);
        }
      }
      if ($new_info->permissions) {
        foreach ($new_info->permissions as $permissionId) {
          $permission = Permission::find($permissionId);
          $user->permissions()->attach($permission);
        }
      }
      Bank::create([
        'user_id' => $user->id,
        'type' => $new_info->bank->type ? $new_info->bank->type : config('wse.bank_type')[0],
        'account_number' => $new_info->bank->account_number,
        'name' => $new_info->bank->name,
      ]);
      Vehicle::create([
        'type' => $new_info->vehicle->type ? $new_info->vehicle->type : config('wse.vehicle_type')[0],
        'brand' => $new_info->vehicle->brand,
        'license_plates' => $new_info->vehicle->license_plates,
        'user_id' => $user->id
      ]);
      IdentityCardPassport::create([
        'user_id' => $user->id,
        'type' => $new_info->identity_card_passport->type ? $new_info->identity_card_passport->type : config('wse.identity_type')[0],
        'code' => $new_info->identity_card_passport->code,
        'efective_date' => date('Y-m-d', strtotime($new_info->identity_card_passport->efective_date)),
        'issued_by' => $new_info->identity_card_passport->issued_by,
      ]);
      Education::create([
        'user_id' => $user->id,
        'school' => $new_info->education->school,
        'specialized' => $new_info->education->specialized,
        'graduation_years' => $new_info->education->graduation_years,
      ]);
      $lib = new UserLib();
      $lib->addPerson($user);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public static function updateInfoExtend($new_info, $user)
  {
    $user->bank->update([
      'type' => $new_info->bank['type'],
      'account_number' => $new_info->bank['account_number'],
      'name' => $new_info->bank['name'],
    ]);
    $user->identity_card_passport->update([
      'type' => $new_info->identity_card_passport['type'],
      'issued_by' => $new_info->identity_card_passport['issued_by'],
      'code' => $new_info->identity_card_passport['code'],
      'efective_date' => date('Y-m-d', strtotime($new_info->identity_card_passport['efective_date'])),
    ]);
    $user->education->update([
      'school' => $new_info->education['school'],
      'specialized' => $new_info->education['specialized'],
      'graduation_years' => $new_info->education['graduation_years'],
    ]);
    $user->vehicle->update([
      'type' => $new_info->vehicle['type'],
      'brand' => $new_info->vehicle['brand'],
      'license_plates' => $new_info->vehicle['license_plates'],
    ]);
    foreach ($user->roles()->get() as $role) {
      if (in_array($role->id, $new_info->roles) == false) {
        $delete_role = Role::find($role->id);
        $user->roles()->detach($delete_role);
      }
    }
    foreach ($new_info->roles as $roleId) {
      $role = Role::find($roleId);
      if ($user->hasRole($role->slug) == false) {
        $user->roles()->save($role);
      }
    }
    foreach ($new_info->permissions as $perId) {
      if (in_array($perId, $new_info->roles_has_pers) == false) {
        $per = Permission::find($perId);
        if ($user->hasPermission($per) == false) {
          $user->permissions()->save($per);
        }
      }
    }
    foreach ($user->permissions()->get() as $per) {
      if (in_array($per->id, $new_info->permissions) == false) {
        $delete_per = Permission::find($per->id);
        $user->permissions()->detach($delete_per);
      }
    }
  }

  public static function getAllDayOfMonth($year, $month) {
    $list = array();

    for ($d = 1; $d <= 31; $d++) {
      $time = mktime(12, 0, 0, $month, $d, $year);
      if (date('m', $time) == $month)
        $list[] = date('Y-m-d', $time);
    }
    return $list;
  }

  public static function isWeeken($date) {
    return (date('N', strtotime($date)) == 7);
  }

  public static function isHoliday($date) {
    $date = date('Y-m-d', strtotime($date));
    $holiday = Holiday::whereDate('start_date', '<=' , $date)->whereDate('end_date', '>=', $date)->get();
    // return $holiday;
    if(count($holiday)) {
      return true;
    }
    return false;
  }

  public static function isNPCL($user_code, $date) {
    $date = date('Y-m-d', strtotime($date));
    $formLeave = FormLeave::where('user_code', $user_code)->where('status', 'accept')
      ->whereDate('begin_leave_date', '<=', $date)->whereDate('end_leave_date', '>=', $date)->with('leave_type')->first();
    if($formLeave) {
      if($formLeave->leave_type->has_salary == 1) {
        return true;
      }
      return false;
    }
    return false;
  }

  public static function isNPKL($user_code, $date) {
    $date = date('Y-m-d', strtotime($date));
    $formLeave = FormLeave::where('user_code', $user_code)->where('status', 'accept')
      ->whereDate('begin_leave_date', '<=', $date)->whereDate('end_leave_date', '>=', $date)->with('leave_type')->first();
    if($formLeave) {
      if($formLeave->leave_type->has_salary == 0) {
        return true;
      }
      return false;
    }
    return false;
  }

}
