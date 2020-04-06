<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormRequestsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('form_requests', function (Blueprint $table) {
      $table->increments('id');
      $table->string('user_code');
      $table->foreign('user_code')->references('employee_code')->on('users')->onDelete('cascade');
      $table->integer('event_id')->nullable()->unsigned();
      $table->foreign('event_id')->references('id')->on('events');
      $table->date('leave_date')->nullable();
      $table->date('work_date')->nullable();
      $table->time('leave_time_begin')->nullable();
      $table->time('leave_time_end')->nullable();
      $table->time('work_time_begin')->nullable();
      $table->time('work_time_end')->nullable();
      $table->enum('type', config('wse.form_request_type'));
      $table->enum('status', config('wse.form_request_status'));
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
    Schema::dropIfExists('form_requests');
  }
}
