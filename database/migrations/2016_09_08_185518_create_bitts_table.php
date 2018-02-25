<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBittsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('script');
            $table->unsignedInteger('user_id');
            $table->boolean('official')->nullable()->default(0);
            $table->boolean('verified')->nullable()->default(0);
            $table->longText('description');
            $table->boolean('private');
            $table->unsignedInteger('uses')->default(0);
            $table->unsignedInteger('stars')->default(0);

            $table->timestamps();

            // This is because we should stick these into elasticsearch
            $table->index(['private']);
        });

        Schema::create('bitt_system', function (Blueprint $table) {
            $table->unsignedInteger('bitt_id');
            $table->unsignedInteger('system_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitts');
        Schema::dropIfExists('bitt_system');
    }
}
