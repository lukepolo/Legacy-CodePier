<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('provider');
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
        Schema::dropIfExists('user_login_providers');
    }
}
