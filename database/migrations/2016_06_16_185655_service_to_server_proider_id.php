<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceToServerProiderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_server_providers', function(Blueprint $table) {
            $table->integer('service')->change();
            $table->renameColumn('service', 'server_provider_id');
        });

        Schema::table('servers', function(Blueprint $table) {
            $table->integer('service')->change();
            $table->renameColumn('service', 'server_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_server_providers', function(Blueprint $table) {
            $table->renameColumn('server_provider_id', 'service');
        });

        Schema::table('servers', function(Blueprint $table) {
            $table->renameColumn('server_provider_id', 'service');
        });
    }
}
