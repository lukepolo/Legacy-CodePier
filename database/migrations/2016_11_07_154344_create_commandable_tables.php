<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandableTables extends Migration
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
            $table->unsignedInteger('site_id')->nullable();
            $table->unsignedInteger('server_id')->nullable();
            $table->string('status')->nullable();
            $table->string('description');
            $table->unsignedInteger('commandable_id')->nullable();
            $table->string('commandable_type')->nullable();

            $table->timestamps();

            $table->index('site_id');
            $table->index('server_id');
            $table->index(['commandable_id', 'commandable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commands');
    }
}
