<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->user()->hasRole('manager')) {
      $groups = Group::whereNull('parent_id')
        ->with('children')
        ->get();

      return response()->json($groups);
    }

    return response([
      'status' => false,
      'message' => 'You don\'t have permission to view oranization!'
    ], 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    if ($request->user()->hasRole('manager')) {
      $validator = Validator::make($request->all(), [
        'name' => 'required'
      ]);
      if ($validator->fails()) {
        return response([
          'status' => false,
          'message' => 'Tạo nhóm mới thất bại!'
        ], 200);
      }
      $group = new Group();
      $group->name = $request->input('name');
      if ($request->input('parent_id')) {
        $group_parent = Group::findOrFail($request->input('parent_id'));
        if ($group_parent) {
          $group->parent_id = $group_parent->id;
        }
      }

      if ($group->save()) {
        $groups= Group::whereNull('parent_id')
          ->with('children')
          ->get();

        return response()->json($groups);
      }
    }
    return response([
      'status' => false,
      'message' => 'Xin lỗi bạn không có quyền tạo quyền này!'
    ], 200);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Group  $group
   * @return \Illuminate\Http\Response
   */
  public function show(Group $group)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Group  $group
   * @return \Illuminate\Http\Response
   */
  public function edit(Group $group)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Group  $group
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Group $group)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Group  $group
   * @return \Illuminate\Http\Response
   */
  public function destroy(Group $group)
  {
    //
  }
}
