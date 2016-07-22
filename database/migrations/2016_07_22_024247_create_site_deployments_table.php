<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_deployments', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('started')->default(0);
            $table->integer('site_id');
            $table->boolean('repository_cloned')->default(0);
            $table->boolean('vendors_installed')->default(0);
            $table->boolean('migrations_ran')->default(0);
            $table->boolean('folders_setup')->default(0);
            $table->boolean('failed')->default(0);
            $table->longText('log');
            $table->longText('error_log');
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
        Schema::drop('site_deployments');
    }
}
