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
            $table->integer('server_id');
            $table->json('log')->nullable();
            $table->integer('site_deployment_id');
            $table->timestamps();
        });

        Schema::table('site_deployments', function (Blueprint $table) {
            $table->dropColumn('log');
            $table->dropColumn('server_id');
        });

        Schema::table('deployment_events', function (Blueprint $table) {
            $table->dropColumn('site_deployment_id');
            $table->integer('site_server_deployment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_deployments', function (Blueprint $table) {
            $table->integer('server_id');
            $table->json('log')->nullable();
        });

        Schema::dropIfExists('site_server_deployments');
    }
}
