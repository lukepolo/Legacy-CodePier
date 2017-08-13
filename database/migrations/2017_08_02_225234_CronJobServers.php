<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CronJobServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->json('servers')->nullable();
            $table->json('server_types')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->dropColumn('servers');
            $table->dropColumn('server_types');
        });
    }
}
