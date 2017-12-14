<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullablePathsSslCerts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ssl_certificates', function (Blueprint $table) {
            $table->string('key_path')->nullable()->change();
            $table->string('cert_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ssl_certificates', function (Blueprint $table) {
            $table->string('key_path')->nullable()->change();
            $table->string('cert_path')->nullable()->change();
        });
    }
}
