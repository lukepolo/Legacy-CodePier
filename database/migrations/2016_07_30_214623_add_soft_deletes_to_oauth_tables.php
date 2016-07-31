<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToOauthTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_login_providers', function(Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('user_notification_providers', function(Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('user_repository_providers', function(Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('user_server_providers', function(Blueprint $table) {
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
        //
    }
}
