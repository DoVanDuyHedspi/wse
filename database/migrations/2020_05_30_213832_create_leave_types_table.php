<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTypesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('leave_types', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('slug');
      $table->string('description');
      $table->integer('number_days');
      $table->boolean('has_salary')->default(0);
      $table->boolean('has_accumulated')->default(0);
      $table->date('expiry_date')->nullable();
      $table->enum('type', ['year', 'month'])->default('year');
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
    Schema::dropIfExists('leave_types');
  }
}
