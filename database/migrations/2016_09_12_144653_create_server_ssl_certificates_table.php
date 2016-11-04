<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerSslCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_ssl_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_ssl_certificate_id')->nullable();
            $table->integer('server_id');
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
        Schema::dropIfExists('server_ssl_certificates');
    }
}
