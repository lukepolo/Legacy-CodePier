<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserServerProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_server_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('server_provider_id');
            $table->string('provider_id');
            $table->string('token');
            $table->string('refresh_token')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('tokenSecret')->nullable();
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
        Schema::drop('user_server_providers');
    }
}
