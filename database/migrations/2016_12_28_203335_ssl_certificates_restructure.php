<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SslCertificatesRestructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE ssl_certificates LIKE site_ssl_certificates;');
        Schema::table('ssl_certificates', function (Blueprint $table) {
            $table->dropColumn('site_id');
        });

        Schema::create('sslCertificateables', function (Blueprint $table) {
            $table->integer('ssl_certificate_id');
            $table->integer('sslCertificateable_id');
            $table->string('sslCertificateable_type');
            $table->index(['ssl_certificate_id', 'sslCertificateable_id', 'sslCertificateable_type'], 'sslCertificateables_indexs');
        });

        $records = DB::table('server_ssl_certificates')->get();

        foreach ($records as $record) {
            $server = \App\Models\Server\Server::withTrashed()->find($record->id);

            if (! empty($server)) {
                unset($record->site_ssl_certificate_id);

                unset($record->id);
                unset($record->server_id);

                $newRecord = \App\Models\SslCertificate::create((array) $record);

                $server->sslCertificates()->save($newRecord);
            }
        }

        $records = DB::table('site_ssl_certificates')->get();

        foreach ($records as $record) {
            $site = \App\Models\Site\Site::withTrashed()->find($record->id);

            if (! empty($site)) {
                unset($record->id);
                unset($record->site_id);

                $newRecord = \App\Models\SslCertificate::create((array) $record);

                $site->sslCertificates()->save($newRecord);
            }
        }

        Schema::dropIfExists('server_ssl_certificates');
        Schema::dropIfExists('site_ssl_certificates');
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
