<?php

namespace App\Http\Controllers\Api;

use App\Branch;
use App\EmployeeType;
use App\Group;
use App\Permission;
use App\Position;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SettingCompany;
use Exception;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api'); //bắt buộc khi sử dụng phải đăng nhập
  }

  public function getInfo()
  {
    $positions = Position::all();
    $branches = Branch::all();
    foreach ($branches as $branch) {
      $branch->imageUrl = $branch->getFirstMediaUrl('images');
    }
    $groups = Group::whereNull('parent_id')
      ->with('children')
      ->get();
    $employee_types = EmployeeType::all();
    $permissions = Permission::all();
    $roles = Role::with('permissions')->get();
    $data['branches'] = $branches;
    $data['positions'] = $positions;
    $data['groups'] = $groups;
    $data['employee_types'] = $employee_types;
    $data['permissions'] = $permissions;
    $data['roles'] = $roles;
    return response()->json($data);
  }

  public function settingTimekeeping(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'morning_begin' => 'required | regex:/(\d+\:\d+)/',
      'morning_late' => 'required | regex:/(\d+\:\d+)/',
      'morning_end' => 'required | regex:/(\d+\:\d+)/',
      'afternoon_begin' => 'required | regex:/(\d+\:\d+)/',
      'afternoon_late' => 'required | regex:/(\d+\:\d+)/',
      'afternoon_end' => 'required | regex:/(\d+\:\d+)/',
    ]);
    if ($validator->fails()) {

      return response([
        'status' => false,
        'message' => 'Thiết lập thất bại!'
      ], 200);
    }
    $timekeeping = SettingCompany::where('type', 'timekeeping')->first();
    if (!$timekeeping) {
      $timekeeping = new SettingCompany();
      $timekeeping->type = 'timekeeping';
    }
    $value = [];
    $value['morning_begin'] = date('H:i', strtotime($request->morning_begin));
    $value['morning_late'] = date('H:i', strtotime($request->morning_late));
    $value['morning_end'] = date('H:i', strtotime($request->morning_end));
    $value['afternoon_begin'] = date('H:i', strtotime($request->afternoon_begin));
    $value['afternoon_late'] = date('H:i', strtotime($request->afternoon_late));
    $value['afternoon_end'] = date('H:i', strtotime($request->afternoon_end));
    $timekeeping->value = $value;
    $timekeeping->save();
    return response(['status' => true, 'timekeeping' => $timekeeping]);
  }

  public function getTimekeeping()
  {
    $timekeeping = SettingCompany::where('type', 'timekeeping')->first();
    return $timekeeping->value;
  }

  public function timeUpdateTimekeepingData()
  {
    try {
      $fetch_data_info = SettingCompany::where('type', 'fetch_data_info')->first();
      $value = $fetch_data_info->value;
      return response(['status'=>true, 'fetch_at' => $value['fetch_at']], 200);
    } catch(Exception $e) {
      abort(404);
    }
  }
}
