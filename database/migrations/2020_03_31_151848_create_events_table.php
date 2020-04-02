<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->string('user_code');
            $table->foreign('user_code')->references('employee_code')->on('users')->onDelete('cascade');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->integer('ILM')->default(0);
            $table->integer('ILA')->default(0);
            $table->integer('LEM')->default(0);
            $table->integer('LEA')->default(0);
            $table->integer('QQD')->default(0);
            $table->integer('QQV')->default(0);
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
        Schema::dropIfExists('events');
    }
}
