<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGitUrlToProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repository_providers', function(Blueprint $table) {
            $table->string('url');
            $table->string('git_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repository_providers', function(Blueprint $table) {
            $table->dropColumn('url');
            $table->dropColumn('git_url');
        });
    }
}
