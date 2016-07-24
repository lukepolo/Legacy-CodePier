<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsForOauth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_login_providers', function(Blueprint $table) {
            $table->string('token');
            $table->string('refresh_token')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('tokenSecret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_login_providers', function(Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('refresh_token');
            $table->dropColumn('expires_in');
            $table->dropColumn('tokenSecret');
        });
    }
}
