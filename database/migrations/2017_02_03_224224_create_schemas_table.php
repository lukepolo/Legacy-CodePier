<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schemas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('database');

            $table->timestamps();

            $table->index(['name', 'database']);
        });

        Schema::create('schemables', function (Blueprint $table) {
            $table->unsignedInteger('schema_id');
            $table->unsignedInteger('schemable_id');
            $table->string('schemable_type');

            $table->index(['schema_id', 'schemable_id', 'schemable_type'], 'schema_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schemas');
        Schema::dropIfExists('schemables');
    }
}
