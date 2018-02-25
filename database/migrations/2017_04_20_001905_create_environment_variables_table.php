<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

            $table->index('variable');
        });

        Schema::create('environmentables', function (Blueprint $table) {
            $table->unsignedInteger('environment_variable_id');
            $table->unsignedInteger('environmentable_id');
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
