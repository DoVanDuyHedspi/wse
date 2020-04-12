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
use App\Lib\UserLib;
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
    if ($request->user()->can('view-users')) {
      $filter = $request->query();
      if (count($filter)) {
        $users = User::getRelationships()->filter($filter)->get();
      } else {
        $users = User::getRelationships()->get();
      };
      // dd(count($users));
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
    if ($request->user()->can('create-users')) {
      $positions = Position::all();
      $branches = Branch::all();
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
    return response([
      'status' => false,
      'type' => 'permission',
      'message' => 'You don\'t have permission to create user!'
    ], 200);
  }

  public function store(Request $request)
  {
    if ($request->user()->can('create-users')) {
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
      $user->api_token = uniqid(base64_encode(str_random(60)));
      $user->salary_rank_id = 1;
      if ($user->save()) {
        $result = UserHelper::createInfoExtend($new_user, $user, $checkImage);
        if ($result) {
          return response(['status' => true, 'user' => $user], 200);
        } else {
          $user->delete();
          return response([
            'status' => false,
            'message' => 'Tạo nhân viên mới thất bại!'
          ], 200);
        }
      }
    }

    return response([
      'status' => false,
      'message' => 'Xin lỗi bạn không có quyền tạo nhân viên!'
    ], 200);
  }

  public function edit($id)
  {
    if (Auth::user()->id == $id || Auth::user()->can('update-users')) {
      $user = User::where('id', $id)->getRelationships()->first();
      try {
        $avatar = $user->getFirstMediaUrl('avatar');
        $user['avatar'] = $avatar;
      } catch (Exception $e) {
        $user['avatar'] = '';
      }
      $user['can_update_adv'] = false;
      if (Auth::user()->can('update-users')) {
        $user['can_update_adv'] = true;
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
    try {
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
        $lib = new UserLib();
        $lib->editPerson($user);

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
      UserHelper::updateInfoExtend($request, $user);
      return response(['status' => true], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }

  public function destroy($id)
  {
    try {
      if (Auth::user()->can('delete-users')) {
        $user = User::find($id);
        $user->clearMediaCollection('avatar');
        $lib = new UserLib();
        $lib->deletePerson($user);
        $user->delete();
        return response([
          'status' => true
        ], 200);
      }
      return response([
        'status' => false,
        'message' => 'Bạn không có quyền xóa thành viên!'
      ], 200);
    } catch (Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
      ], 200);
    }
  }
}
