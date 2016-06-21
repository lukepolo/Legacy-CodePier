<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserRepositoryProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_repository_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('server_provider_id');
            $table->string('provider_id');
            $table->string('service');
            $table->string('token');
            $table->string('refresh_token');
            $table->string('expires_in');
            $table->string('tokenSecret')->nullable();
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
        \Schema::drop('user_repository_providers');
    }
}
