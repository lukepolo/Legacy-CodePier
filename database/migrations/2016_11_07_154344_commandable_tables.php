<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommandableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id');
            $table->string('type');
            $table->boolean('started')->default(0);
            $table->boolean('failed')->default(0);
            $table->boolean('completed')->default(0);
            $table->string('runtime')->nullable();
            $table->longText('log')->nullable();
            $table->timestamps();
        });

        Schema::create('commandables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('command_id');
            $table->integer('commandable_id');
            $table->string('commandable_type');
        });

        Schema::dropIfExists('events');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commands');
        Schema::dropIfExists('commandables');
    }
}
