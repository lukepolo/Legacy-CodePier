<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaemonTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daemons', function (Blueprint $table) {
            $table->string('user');
            $table->increments('id');
            $table->string('command');
            $table->json('server_ids')->nullable();
            $table->json('server_types')->nullable();
            $table->timestamps();
        });

        Schema::create('daemonables', function (Blueprint $table) {
            $table->integer('daemon_id');
            $table->integer('daemonable_id');
            $table->string('daemonable_type');
            $table->index(['daemon_id', 'daemonable_id', 'daemonable_type'], 'daemonable_indexs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daemons');
        Schema::dropIfExists('daemonables');
    }
}
