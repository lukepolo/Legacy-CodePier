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

        Schema::create('environment_variableables', function (Blueprint $table) {
            $table->integer('environment_variable_id');
            $table->integer('environmentVariable_id');
            $table->string('environmentVariable_type');
            $table->index(['environment_variable_id', 'environmentVariable_id', 'environmentVariable_type'], 'env_variables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('environment_variables');
        Schema::dropIfExists('environment_variableables');
    }
}
