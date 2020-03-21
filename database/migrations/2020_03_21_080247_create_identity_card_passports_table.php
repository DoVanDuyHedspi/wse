<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentityCardPassportsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('identity_card_passports', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->enum('type', ['CMND', 'Hộ chiếu']);
      $table->string('code');
      $table->date('efective_date');
      $table->date('expiry_date')->nullable();
      $table->string('issued_by');
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
    Schema::dropIfExists('identity_card_passports');
  }
}
