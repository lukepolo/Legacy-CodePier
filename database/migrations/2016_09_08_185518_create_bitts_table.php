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
            $table->string('name');
            $table->longText('script');
            $table->integer('user_id');
            $table->string('system')->nullable();
            $table->string('version')->nullable();
            $table->boolean('official')->nullable()->default(0);
            $table->boolean('approved')->nullable()->default(0);
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
        Schema::drop('bitts');
    }
}
