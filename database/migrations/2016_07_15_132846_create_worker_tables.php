<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerTables extends Migration
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
            $table->boolean('auto_start');
            $table->boolean('auto_restart');
            $table->integer('number_of_workers');
            $table->timestamps();
        });

        Schema::create('workerables', function (Blueprint $table) {
            $table->integer('worker_id');
            $table->integer('workerable_id');
            $table->string('workerable_type');
            $table->index(['worker_id', 'workerable_id', 'workerable_type'], 'workerable_indexs');
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
