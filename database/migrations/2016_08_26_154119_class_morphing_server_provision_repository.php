<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClassMorphingServerProvisionRepository extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->string('system_class');
        });

        Schema::table('server_providers', function (Blueprint $table) {
            $table->string('server_class');
        });

        Schema::table('repository_providers', function (Blueprint $table) {
            $table->string('repository_class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('system_class');
        });

        Schema::table('server_providers', function (Blueprint $table) {
            $table->dropColumn('server_class');
        });

        Schema::table('repository_providers', function (Blueprint $table) {
            $table->dropColumn('repository_class');
        });
    }
}
