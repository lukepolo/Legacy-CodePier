<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteSSLCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_ssl_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id');
            $table->string('type');
            $table->string('domains');
            $table->boolean('active')->default(0);
            $table->string('key_path');
            $table->string('cert_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_ssl_certificates');
    }
}
