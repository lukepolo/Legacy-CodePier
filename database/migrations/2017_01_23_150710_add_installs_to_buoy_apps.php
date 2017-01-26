<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInstallsToBuoyApps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buoy_apps', function (Blueprint $table) {
            $table->integer('installs')->unsigned()->default(0)->after('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buoy_apps', function (Blueprint $table) {
            $table->dropColumn('installs');
        });
    }
}
