<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BittsInstallsStars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bitts', function (Blueprint $table) {
            $table->integer('uses')->default(0);
            $table->integer('stars')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bitts', function (Blueprint $table) {
            $table->dropColumn('uses');
            $table->dropColumn('stars');
        });
    }
}
