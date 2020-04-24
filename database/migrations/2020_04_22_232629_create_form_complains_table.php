<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormComplainsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('form_complains', function (Blueprint $table) {
      $table->increments('id');
      $table->string('user_code');
      $table->foreign('user_code')->references('employee_code')->on('users')->onDelete('cascade');
      $table->date('date');
      $table->time('begin_time');
      $table->time('end_time');
      $table->enum('result', config('wse.form_check_camera_result'));
      $table->enum('status', config('wse.form_check_camera_status'));
      $table->string('note');
      $table->string('reply')->nullable();
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
    Schema::dropIfExists('form_complains');
  }
}
