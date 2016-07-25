<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGitCommitToSiteDeployments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_deployments', function(Blueprint $table) {
            $table->string('git_commit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_deployments', function(Blueprint $table) {
            $table->dropColumn('git_commit');
        });
    }
}
