<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
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
  public function index()
  {
    $branches = Branch::all();
    foreach($branches as $branch) {
      $branch->imageUrl = $branch->getFirstMediaUrl('images');
    }
    return response()->json($branches);
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
    if ($request->user()->hasRole('manager')) {
      $validator = Validator::make($request->all(), [
        'data' => 'required',
        'image' => 'required'
      ]);
      if ($validator->fails()) {

        return response([
          'status' => false,
          'message' => 'Tạo chi nhánh mới thất bại!'
        ], 200);
      }
      $branch = new Branch();
      $data = json_decode($request->input('data'));
      $branch->name = $data->name;
      $branch->description = $data->description;
      $branch->save();
      if($request->hasFile('image') && $request->file('image')->isValid()) {
        $branch->addMediaFromRequest('image')->toMediaCollection('images');
      }
      $branch->imageUrl = $branch->getFirstMediaUrl('images');

      return response()->json($branch);
    }

    return response([
      'status' => false,
      'message' => 'Xin lỗi bạn không có quyền tạo chi nhánh!'
    ], 200);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function show(Branch $branch)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function edit(Branch $branch)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Branch $branch)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Branch  $branch
   * @return \Illuminate\Http\Response
   */
  public function destroy(Branch $branch)
  {
    //
  }
}
