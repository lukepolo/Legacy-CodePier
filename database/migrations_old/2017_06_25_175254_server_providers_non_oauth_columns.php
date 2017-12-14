<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServerProvidersNonOauthColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('server_providers', function (Blueprint $table) {
            $table->boolean('oauth')->default(1);
            $table->boolean('secret_token')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('server_providers', function (Blueprint $table) {
            $table->dropColumn('oauth');
            $table->dropColumn('secret_token');
        });
    }
}
