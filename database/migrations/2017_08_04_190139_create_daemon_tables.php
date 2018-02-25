<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

            $table->index(['user', 'command']);
        });

        Schema::create('daemonables', function (Blueprint $table) {
            $table->unsignedInteger('daemon_id');
            $table->unsignedInteger('daemonable_id');
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
