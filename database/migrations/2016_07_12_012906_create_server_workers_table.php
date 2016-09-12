<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_workers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id');
            $table->string('command');
            $table->string('user');
            $table->boolean('auto_start');
            $table->boolean('auto_restart');
            $table->integer('number_of_workers');
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
        Schema::drop('server_workers');
    }
}
