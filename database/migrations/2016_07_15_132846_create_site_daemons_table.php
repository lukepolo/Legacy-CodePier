<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteDaemonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_workers', function (Blueprint $table) {
            $table->string('user');
            $table->increments('id');
            $table->string('command');
            $table->integer('site_id');
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
        Schema::dropIfExists('site_workers');
    }
}
