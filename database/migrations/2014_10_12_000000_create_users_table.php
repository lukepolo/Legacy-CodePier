<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('referrer')->nullable();
            $table->string('role')->default('user');
            $table->boolean('workflow')->default(1);
            $table->string('password')->nullable();
            $table->unsignedInteger('current_pile_id')->nullable();
            $table->string('user_login_provider_id')->nullable();
            $table->string('second_auth_secret')->nullable();
            $table->boolean('second_auth_active')->default(false);
            $table->timestamp('second_auth_updated_at')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index('user_login_provider_id');
            $table->unique(['email', 'user_login_provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
