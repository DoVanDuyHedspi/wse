<?php

namespace App\Http\Controllers;

use App\Branch;
use App\EmployeeType;
use App\Group;
use App\Permission;
use App\Position;
use App\Role;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth'); //bắt buộc khi sử dụng phải đăng nhập
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
}
