<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormLeavesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('form_leaves', function (Blueprint $table) {
      $table->increments('id');
      $table->string('user_code');
      $table->foreign('user_code')->references('employee_code')->on('users')->onDelete('cascade');
      $table->integer('leave_type_id')->unsigned();
      $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
      $table->date('begin_leave_date');
      $table->date('end_leave_date');
      $table->integer('number_days');
      $table->enum('status', ['waiting', 'accept', 'refuse']);
      $table->string('approver_code')->nullable();
      $table->foreign('approver_code')->references('employee_code')->on('users')->onDelete('cascade');
      $table->string('reason');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('form_leaves');
  }
}
