<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserServerProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('passport:install');

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

            $table->index('user_id');
            $table->index(['server_provider_id', 'provider_id'], 'oauth_index');
        });

        Schema::table('user_server_providers', function (Blueprint $table) {
            $table->renameColumn('tokenSecret', 'token_secret');
        });

        Schema::table('user_server_providers', function (Blueprint $table) {
            $table->longText('token')->change();
            $table->longText('token_secret')->change();
            $table->longText('refresh_token')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_server_providers');
    }
}
