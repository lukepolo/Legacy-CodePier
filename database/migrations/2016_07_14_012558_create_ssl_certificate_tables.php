<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSllCertificateTables extends Migration
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
            $table->string('type');
            $table->string('domains');
            $table->boolean('active')->default(0);
            $table->string('key_path');
            $table->string('cert_path');
            $table->longText('key')->nullable();
            $table->longText('cert')->nullable();
            $table->timestamps();
        });

        Schema::create('sslCertificateables', function (Blueprint $table) {
            $table->integer('ssl_certificate_id');
            $table->integer('sslCertificateable_id');
            $table->string('sslCertificateable_type');
            $table->index(['ssl_certificate_id', 'sslCertificateable_id', 'sslCertificateable_type'], 'sslCertificateables_indexs');
        });

//            ['type', 'domains'],
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
