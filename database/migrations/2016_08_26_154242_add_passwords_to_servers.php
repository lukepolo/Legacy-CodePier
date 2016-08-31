<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPasswordsToServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servers',function(Blueprint $table) {
            $table->longText('root_password')->nullable();
            $table->longText('database_password')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers',function(Blueprint $table) {
            $table->dropColumn('root_password');
            $table->dropColumn('database_password');
        });
    }
}
