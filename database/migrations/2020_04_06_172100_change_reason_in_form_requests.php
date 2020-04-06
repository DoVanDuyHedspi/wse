<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeReasonInFormRequests extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('form_requests', function (Blueprint $table) {
      $table->dropColumn('reason');
    });

    Schema::table('form_requests', function (Blueprint $table) {
      $table->string('reason')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('form_requests', function (Blueprint $table) {
      //
    });
  }
}
