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
        Schema::create('ssl_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('domains');
            $table->boolean('active')->default(0);
            $table->longText('key')->nullable();
            $table->longText('cert')->nullable();
            $table->string('key_path')->nullable();
            $table->string('cert_path')->nullable();
            $table->boolean('failed')->default(0);

            $table->timestamps();

            $table->index(['type', 'domains']);
        });

        Schema::create('sslCertificateables', function (Blueprint $table) {
            $table->unsignedInteger('ssl_certificate_id');
            $table->unsignedInteger('sslCertificateable_id');
            $table->string('sslCertificateable_type');
            $table->index(['ssl_certificate_id', 'sslCertificateable_id', 'sslCertificateable_type'], 'sslCertificateables_indexs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ssl_certificates');
        Schema::dropIfExists('sslCertificateables');
    }
}
