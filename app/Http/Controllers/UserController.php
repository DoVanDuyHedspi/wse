<?php

namespace App\Http\Controllers;

use App\Branch;
use App\EmployeeType;
use App\Group;
use App\Position;
use Illuminate\Http\Request;
use App\User;
use Exception;
// use Dotenv\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth'); //bắt buộc khi sử dụng phải đăng nhập
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
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

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    if (Auth::user()->id == $id) {
      $user = User::where('id', $id)->getRelationships()->first();
      return response()->json($user);
    }
    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view this user!'
    ], 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
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
      } catch(Exception $e) {
        $user['avatar'] = '';
      }
      

      return response()->json($user);
    }
    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view this user!'
    ], 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
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

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
