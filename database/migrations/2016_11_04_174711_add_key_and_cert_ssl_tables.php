<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyAndCertSslTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('server_ssl_certificates', function(Blueprint $table) {
            $table->longText('key')->nullable();
            $table->longText('cert')->nullable();
        });

        Schema::table('site_ssl_certificates', function(Blueprint $table) {
            $table->longText('key')->nullable();
            $table->longText('cert')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('server_ssl_certificates', function(Blueprint $table) {
            $table->dropColumn('key');
            $table->dropColumn('cert');
        });

        Schema::table('site_ssl_certificates', function(Blueprint $table) {
            $table->dropColumn('key');
            $table->dropColumn('cert');
        });
    }
}
