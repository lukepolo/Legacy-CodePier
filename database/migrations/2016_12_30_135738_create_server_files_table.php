<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_files', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('file_path');
            $table->longText('content')->nullable();
            $table->integer('server_id');
            $table->integer('custom')->default(0);
            $table->timestamps();

            $table->index(['server_id', 'custom']);
        });

        Schema::table('site_files', function(Blueprint $table) {
            $table->integer('custom')->default(0);

            $table->index(['site_id', 'custom']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_files');

        Schema::table('site_files', function(Blueprint $table) {
            $table->dropColumn('custom');
        });
    }
}
