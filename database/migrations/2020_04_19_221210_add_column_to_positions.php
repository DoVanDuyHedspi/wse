<?php

use App\Position;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToPositions extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('positions', function (Blueprint $table) {
      $table->time('begin_allowed_access');
      $table->time('end_allowed_access');
    });
    $positions = Position::all();
    foreach($positions as $position) {
      $position->begin_allowed_access = date('H:i', strtotime('06:00'));
      $position->end_allowed_access = date('H:i', strtotime('20:00'));
      $position->save();
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('positions', function (Blueprint $table) {
      $table->dropColumn(['begin_allowed_access']);
      $table->dropColumn(['end_allowed_access']);
    });
  }
}
