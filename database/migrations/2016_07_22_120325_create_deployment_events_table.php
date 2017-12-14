<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeploymentEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deployment_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deployment_step_id');
            $table->boolean('started')->default(0);
            $table->boolean('failed')->default(0);
            $table->boolean('completed')->default(0);
            $table->string('runtime')->nullable();
            $table->json('log')->nullable();
            $table->timestamps();

            $table->integer('site_server_deployment_id');

            $table->index('deployment_step_id');
            $table->index('site_server_deployment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deployment_events');
    }
}
