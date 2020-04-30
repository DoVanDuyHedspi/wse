<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToFakeFaceReports extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('fake_face_reports', function (Blueprint $table) {
      $table->string('user_code')->nullable();
      $table->foreign('user_code')->references('employee_code')->on('users')->onDelete('cascade');
      $table->boolean('spoofing_success')->default(0);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('fake_face_reports', function (Blueprint $table) {
      $table->dropColumn(['user_code', 'spoofing_success']);
    });
  }
}
