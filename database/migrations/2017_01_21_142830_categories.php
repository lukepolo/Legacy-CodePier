<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Categories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('categorable', function (Blueprint $table) {
            $table->integer('category_id');
            $table->integer('categorable_id');
            $table->string('categorable_type');
            $table->timestamps();

            $table->index(['category_id', 'categorable_id', 'categorable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('categorable');
    }
}
