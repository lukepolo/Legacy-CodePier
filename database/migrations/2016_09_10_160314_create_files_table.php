<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
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
            $table->text('file_path');
            $table->longText('content')->nullable();
            $table->unsignedInteger('custom')->default(0);
            $table->boolean('framework_file')->default(0);

            $table->timestamps();
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
        Schema::dropIfExists('files');
        Schema::dropIfExists('fileables');
    }
}
