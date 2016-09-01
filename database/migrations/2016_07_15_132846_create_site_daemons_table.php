<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteDaemonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_daemons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id');
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
        Schema::drop('site_daemons');
    }
}
