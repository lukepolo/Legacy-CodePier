<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveFlagToSslCerts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_ssl_certificates', function(Blueprint $table) {
            $table->boolean('active')->default(0);
            $table->string('key_path');
            $table->string('cert_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_ssl_certificates', function(Blueprint $table) {
            $table->dropColumn('active');
            $table->dropColumn('key_path');
            $table->dropColumn('cert_path');
        });
    }
}
