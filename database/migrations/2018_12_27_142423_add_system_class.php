<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSystemClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('systems', function(Blueprint $table) {
            $table->boolean('enabled')->after('name')->default(true);
            $table->boolean('default')->after('name')->default(false);
            $table->string('class')->after('name');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('systems', function(Blueprint $table) {
            $table->dropColumn('class');
            $table->dropColumn('enabled');
        });
    }
}
