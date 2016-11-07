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
            $table->string('name');
            $table->string('status');
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('pile_id');
            $table->string('ip')->nullable();
            $table->json('options')->nullable();
            $table->integer('given_server_id')->nullable();
            $table->integer('server_provider_id');
            $table->integer('team_id')->nullable();
            $table->integer('progress')->default(0);
            $table->text('public_ssh_key')->nullable();
            $table->text('private_ssh_key')->nullable();
            $table->json('server_features')->nullable();
            $table->boolean('ssh_connection')->default(0);
            $table->longText('sudo_password')->nullable();
            $table->longText('database_password')->nullable();
            $table->json('server_provider_features')->nullable();
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
