<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveHookColumnsSite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sites', function(Blueprint $table){
            $table->dropColumn('deploy_key');
            $table->dropColumn('deploy_hook');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites', function(Blueprint $table){
            $table->string('deploy_key')->nullable();
            $table->string('deploy_hook')->nullable();
        });
    }
}
