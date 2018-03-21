<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcmeColumnsToSslCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ssl_certificates', function (Blueprint $table) {
            $table->boolean('wildcard')->default(0)->after('type');
            $table->string('acme_fulldomain')->nullable()->after('failed');
            $table->string('acme_subdomain')->nullable()->after('failed');
            $table->longText('acme_password')->nullable()->after('failed');
            $table->string('acme_username')->nullable()->after('failed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
