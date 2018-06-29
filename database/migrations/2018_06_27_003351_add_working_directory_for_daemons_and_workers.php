<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkingDirectoryForDaemonsAndWorkers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string('working_directory')->nullable();
        });
        Schema::table('daemons', function (Blueprint $table) {
            $table->string('working_directory')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('working_directory');
        });
        Schema::table('daemons', function (Blueprint $table) {
            $table->dropColumn('working_directory');
        });
    }
}
