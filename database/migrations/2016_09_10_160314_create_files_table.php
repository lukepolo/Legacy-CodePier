<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('file_path');
            $table->longText('content')->nullable();
            $table->unsignedInteger('custom')->default(0);
            $table->boolean('framework_file')->default(0);

            $table->timestamps();

            $table->index('file_path');
        });


        Schema::create('fileables', function (Blueprint $table) {
            $table->unsignedInteger('file_id');
            $table->unsignedInteger('fileable_id');
            $table->string('fileable_type');
            $table->index(['file_id', 'fileable_id', 'fileable_type']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_files');
    }
}
