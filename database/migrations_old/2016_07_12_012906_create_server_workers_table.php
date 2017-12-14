<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('site_worker_id')->nullable();
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
        Schema::dropIfExists('server_workers');
    }
}
