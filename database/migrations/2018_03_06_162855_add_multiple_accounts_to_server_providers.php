<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultipleAccountsToServerProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('server_providers', function (Blueprint $table) {
            $table->boolean('multiple_accounts')->default(false)->after('oauth');
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
            $table->dropColumn('multiple_accounts');
        });
    }
}
