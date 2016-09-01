<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Servers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('pile_id');
//            $table->integer('server_id'); // TODO - what is this for?
            $table->string('name');
            $table->string('status');
            $table->integer('server_provider_id');
            $table->string('ip')->nullable();
            $table->integer('progress')->default(0);
            $table->integer('team_id')->nullable();
            $table->text('public_ssh_key')->nullable();
            $table->text('private_ssh_key')->nullable();
            $table->boolean('ssh_connection')->default(0);
            $table->json('options')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('servers');
    }
}
