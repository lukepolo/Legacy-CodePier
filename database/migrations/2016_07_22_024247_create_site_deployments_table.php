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
            $table->integer('server_id');
            $table->string('status');
            $table->json('log')->nullable();
            $table->string('git_commit')->nullable();
            $table->dropColumn('log');
            $table->dropColumn('server_id');
            $table->string('commit_message')->nullable();
            $table->timestamps();
            $table->string('folder_name')->nullable();
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
        Schema::dropIfExists('site_deployments');
    }
}
