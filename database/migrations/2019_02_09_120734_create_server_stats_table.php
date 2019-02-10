<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servers', function(Blueprint $table) {
            $table->dropColumn('stats');
        });

        Schema::create('server_stats', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('server_id')->unique();

            $table->float('disk_threshold')->nullable();
            $table->float('load_threshold')->nullable();
            $table->float('memory_threshold')->nullable();

            $table->json('disk_stats')->nullable();
            $table->json('load_stats')->nullable();
            $table->json('memory_stats')->nullable();

            $table->integer('number_of_cpus')->default(1);

            $table->json('disk_notification_count')->nullable();
            $table->integer('load_notification_count')->nullable();
            $table->json('memory_notification_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_stats');
    }
}
