<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnvironmentVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('environment_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('variable');
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('environmentables', function (Blueprint $table) {
            $table->integer('environment_variable_id');
            $table->integer('environmentable_id');
            $table->string('environmentable_type');
            $table->index(['environment_variable_id', 'environmentable_id', 'environmentable_type'], 'env_variables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('environmentables');
        Schema::dropIfExists('environment_variables');
    }
}
