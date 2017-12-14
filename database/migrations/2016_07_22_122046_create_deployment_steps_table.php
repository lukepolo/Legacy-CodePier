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
            $table->unsignedInteger('site_id');
            $table->string('step');
            $table->unsignedInteger('order');
            $table->longText('script')->nullable();
            $table->string('internal_deployment_function')->nullable();

            $table->json('server_ids')->nullable();
            $table->json('server_types')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('site_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deployment_steps');
    }
}
