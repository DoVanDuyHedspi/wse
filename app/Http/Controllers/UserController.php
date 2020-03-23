<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Branch;
use App\Education;
use App\EmployeeType;
use App\Group;
use App\Permission;
use App\Position;
use App\Role;
use App\Helpers\UserHelper;
use App\IdentityCardPassport;
use Illuminate\Http\Request;
use App\User;
use App\Vehicle;
use Exception;
// use Dotenv\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth'); //bắt buộc khi sử dụng phải đăng nhập
  }

  public function index(Request $request)
  {
    if ($request->user()->hasRole('direct-manager', 'group-manager', 'manager')) {
      $users = User::getRelationships()->get();
      return response()->json($users);
    }

    return response([
      'status' => false,
      'type' => 'permission',
      'message' => 'You don\'t have permission to view users!'
    ], 200);
  }

  public function create(Request $request)
  {
    if ($request->user()->hasRole('group-manager', 'manager')) {
      $positions = Position::all();
      $branches = Branch::all();
      $groups = Group::whereNull('parent_id')
        ->with('children')
        ->get();
      $employee_types = EmployeeType::all();
      $permissions = Permission::all();
      $roles = Role::all();
      $data['branches'] = $branches;
      $data['positions'] = $positions;
      $data['groups'] = $groups;
      $data['employee_types'] = $employee_types;
      $data['permissions'] = $permissions;
      $data['roles'] = $roles;
      return response()->json($data);
    }
    return response([
      'status' => false,
      'type' => 'permission',
      'message' => 'You don\'t have permission to create user!'
    ], 200);
  }

  public function store(Request $request)
  {
    if ($request->user()->hasRole('manager', 'group-manager')) {
      $new_user = json_decode($request->new_user);

      $validator = Validator::make((array) $new_user, [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'branch_id' => 'required',
        'group_id' => 'required',
        'position_id' => 'required',
        'employee_type_id' => 'required',
        'official_start_day' => 'required',
        'roles' => 'required',
      ]);
      $checkImage = $request->hasFile('image') && $request->file('image')->isValid();
      if ($validator->fails() || !$checkImage) {
        return response([
          'status' => false,
          'message' => 'Tạo nhân viên mới thất bại!'
        ], 200);
      };

      $user = new User();
      $user->bindAttrsToUser($new_user);
      $user->employee_code = UserHelper::createEmployeeCode();
      $user->password = Hash::make($new_user->password);
      $user->salary_rank_id = 1;
      if ($user->save()) {
        if ($checkImage) {
          $user->addMediaFromRequest('image')->toMediaCollection('avatar');
        }
        if($new_user->roles) {
          foreach($new_user->roles as $roleId) {
            $role = Role::find($roleId);
            $user->roles()->attach($role);
          }
        }
        if($new_user->permissions) {
          foreach($new_user->permissions as $permissionId) {
            $permission = Role::find($permissionId);
            $user->permissions()->attach($permission);
          }
        }
        Bank::create([
          'user_id' => $user->id,
          'type' => $new_user->bank->type,
          'account_number' => $new_user->bank->account_number,
          'name' => $new_user->bank->name,
        ]);
        Vehicle::create([
          'type' => $new_user->vehicle->type,
          'brand' => $new_user->vehicle->brand,
          'license_plates' => $new_user->vehicle->license_plates,
          'user_id' => $user->id
        ]);
        IdentityCardPassport::create([
          'user_id' => $user->id,
          'type' => $new_user->identity_card_passport->type,
          'code' => $new_user->identity_card_passport->code,
          'efective_date' => date('Y-m-d', strtotime($new_user->identity_card_passport->efective_date)),
          'issued_by' => $new_user->identity_card_passport->issued_by,
        ]);
        Education::create([
          'user_id' => $user->id,
          'school' => $new_user->education->school,
          'specialized' => $new_user->education->specialized,
          'graduation_years' => $new_user->education->graduation_years,
        ]);
      }
      return response(['status' => true], 200);
    }

    return response([
      'status' => false,
      'message' => 'Xin lỗi bạn không có quyền tạo nhân viên!'
    ], 200);
  }

  public function show($id)
  {
    if ((Auth::user()->id == $id) || Auth::user()->hasRole('manager', 'group-manager')) {
      $user = User::where('id', $id)->getRelationships()->first();
      try {
        $avatar = $user->getFirstMediaUrl('avatar');
        $user['avatar'] = $avatar;
      } catch (Exception $e) {
        $user['avatar'] = '';
      }
      return response()->json($user);
    }
    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view this user!'
    ], 200);
  }

  public function edit($id)
  {
    if (Auth::user()->id == $id) {
      $user = User::where('id', $id)->getRelationships()->first();
      $positions = Position::all();
      $branches = Branch::all();
      $groups = Group::whereNull('parent_id')
        ->with('children')
        ->get();
      $employee_types = EmployeeType::all();
      $user['branches'] = $branches;
      $user['positions'] = $positions;
      $user['groups'] = $groups;
      $user['employee_types'] = $employee_types;
      try {
        $avatar = $user->getFirstMediaUrl('avatar');
        $user['avatar'] = $avatar;
      } catch (Exception $e) {
        $user['avatar'] = '';
      }


      return response()->json($user);
    }
    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view this user!'
    ], 200);
  }

  public function update(Request $request, $id)
  {
    $user = User::where('id', $id)->getRelationships()->first();
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      $validator = Validator::make($request->all(), [
        'image' => 'image',
      ]);
      if ($validator->fails()) {
        return response([
          'status' => 'false',
          'message' => 'Hãy chọn đúng file ảnh!'
        ], 200);
      };
      $user->clearMediaCollection('avatar');
      $user->addMediaFromRequest('image')->toMediaCollection('avatar');

      return response()->json(['avatar' => $user->getFirstMediaUrl('avatar')]);
    }
    $validator = Validator::make($request->all(), [
      'email' => 'required',
      'name' => 'required',
    ]);
    if ($validator->fails()) {
      return response([
        'status' => false,
        'message' => 'Cập nhật thông tin nhân viên thất bại!'
      ], 200);
    }
    $user->bindAttrsToUser($request);
    $user->save();
    $user->bank->update([
      'type' => $request->bank['type'],
      'account_number' => $request->bank['account_number'],
      'name' => $request->bank['name'],
    ]);
    $user->identity_card_passport->update([
      'type' => $request->identity_card_passport['type'],
      'issued_by' => $request->identity_card_passport['issued_by'],
      'code' => $request->identity_card_passport['code'],
      'efective_date' => date('Y-m-d', strtotime($request->identity_card_passport['efective_date'])),
    ]);
    $user->education->update([
      'school' => $request->education['school'],
      'specialized' => $request->education['specialized'],
      'graduation_years' => $request->baeducationnk['graduation_years'],
    ]);
    $user->vehicle->update([
      'type' => $request->vehicle['type'],
      'brand' => $request->vehicle['brand'],
      'license_plates' => $request->vehicle['license_plates'],
    ]);

    return response()->json();
  }

  public function destroy($id)
  {
    //
  }
}
