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
        Schema::create('workers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('command');
            $table->string('user');
            $table->boolean('auto_start');
            $table->boolean('auto_restart');
            $table->integer('number_of_workers');
            $table->json('server_ids')->nullable();
            $table->json('server_types')->nullable();

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
        Schema::dropIfExists('workers');
        Schema::dropIfExists('workerables');
    }
}
