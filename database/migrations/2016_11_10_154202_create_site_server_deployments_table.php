<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteServerDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_server_deployments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->unsignedInteger('server_id');
            $table->unsignedInteger('site_deployment_id');
            $table->unsignedInteger('completed')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->boolean('started')->default(0);

            $table->timestamps();

            $table->index('server_id');
            $table->index('site_deployment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_server_deployments');
    }
}
