<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeploymentStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deployment_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id');
            $table->string('step');
            $table->integer('order');
            $table->longText('script')->nullable();
            $table->string('internal_deployment_function')->nullable();
            $table->boolean('customizable');
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
        Schema::drop('deployment_steps');
    }
}
