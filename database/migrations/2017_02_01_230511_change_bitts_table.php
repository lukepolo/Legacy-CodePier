<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBittsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bitts', function(Blueprint $table) {
            $table->renameColumn('name', 'title');
            $table->renameColumn('approved', 'verified');
            $table->dropColumn('system');
            $table->dropColumn('version');
            $table->longText('description');
        });

        Schema::create('bitt_system', function(Blueprint $table) {
            $table->integer('bitt_id');
            $table->integer('system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bitts', function(Blueprint $table) {
            $table->renameColumn('verified', 'approved');
            $table->renameColumn('title', 'name');
            $table->dropColumn('description');
            $table->string('system');
            $table->string('version');
        });

        Schema::dropIfExists('bitt_system');
    }
}
