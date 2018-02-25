<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
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

        Schema::create('categorables', function (Blueprint $table) {
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('categorable_id');
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
        Schema::dropIfExists('categorables');
    }
}
