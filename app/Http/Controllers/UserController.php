<?php

namespace App\Http\Controllers;

use App\Branch;
use App\EmployeeType;
use App\Group;
use App\Position;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

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
      return response()->json(User::all());
    }

    return response([
      'status' => false,
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
      $user = User::where('id', $id)
        ->with('position')
        ->with('branch')
        ->with('group')
        ->with('slary_rank')
        ->with('employee_type')->first();
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
      $user = User::where('id', $id)
        ->with('permissions')
        ->with('roles')
        ->with('position')
        ->with('branch')
        ->with('group')
        ->with('slary_rank')
        ->with('employee_type')->first();
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
    //
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
