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
            $table->integer('site_id');
            $table->string('status');
            $table->integer('server_id');
            $table->text('log')->nullable();
            $table->string('git_commit')->nullable();
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
