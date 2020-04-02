<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFinedTimeToEvents extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('events', function (Blueprint $table) {
      $table->integer('fined_time')->default(0);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('events', function (Blueprint $table) {
      $table->dropColumn(['fined_time']);
    });
  }
}
