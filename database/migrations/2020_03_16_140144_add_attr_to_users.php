<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAttrToUsers extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->string('phone_number')->nullable();
      $table->string('employee_code')->unique();
      $table->string('nationality')->nullable();
      $table->enum('gender', ['Nam', 'Nữ', 'Khác']);
      $table->date('birthday')->nullable();
      $table->date('official_start_day')->nullable();
      $table->unsignedBigInteger('position_id');
      $table->string('tax_code')->nullable();
      $table->unsignedBigInteger('employee_type_id');
      $table->unsignedBigInteger('branch_id');
      $table->unsignedBigInteger('group_id');
      $table->string('personal_email')->nullable();
      $table->string('birthplace')->nullable();
      $table->string('current_address')->nullable();
      $table->string('permanent_address')->nullable();
      $table->unsignedBigInteger('salary_rank_id');
      // $table->json('identity_card_passport')->nullable();
      // $table->json('vehicles')->nullable();
      // $table->json('bank')->nullable();
      // $table->json('education')->nullable();
      $table->foreign('position_id')->references('id')->on('positions');
      $table->foreign('branch_id')->references('id')->on('branches');
      $table->foreign('group_id')->references('id')->on('groups');
      $table->foreign('employee_type_id')->references('id')->on('employee_types');
      $table->foreign('salary_rank_id')->references('id')->on('salary_ranks');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign(['branch_id']);
      $table->dropForeign(['position_id']);
      $table->dropForeign(['employee_type_id']);
      $table->dropForeign(['salary_rank_id']);
      $table->dropForeign(['group_id']);
      $table->dropColumn([
        'phone_number',  'nationality', 'gender', 'birthday', 'official_start_day', 'position_id', 'tax_code', 'employee_type_id',
        'branch_id', 'group_id', 'personal_email', 'birthplace', 'current_address', 'permanent_address', 'salary_rank_id', 'employee_code'
      ]);
    });
  }
}
