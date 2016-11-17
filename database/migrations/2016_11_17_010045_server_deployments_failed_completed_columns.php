<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServerDeploymentsFailedCompletedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_server_deployments', function(Blueprint $table) {
            $table->integer('completed')->default(0);
            $table->integer('failed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_server_deployments', function(Blueprint $table) {
            $table->dropColumn('completed');
            $table->dropColumn('failed');
        });
    }
}
