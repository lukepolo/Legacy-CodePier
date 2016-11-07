<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            $table->integer('site_deployment_id');
            $table->integer('deployment_step_id');
            $table->boolean('started')->default(0);
            $table->boolean('failed')->default(0);
            $table->boolean('completed')->default(0);
            $table->string('runtime')->nullable();
            $table->longText('log')->nullable();
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
        Schema::drop('deployment_events');
    }
}
