<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain');
            $table->integer('user_id');
            $table->string('branch')->nullable();
            $table->text('repository')->nullable();
            $table->text('web_directory')->nullable();
            $table->integer('user_repository_provider_id')->nullable();
            $table->boolean('wildcard_domain')->default(0);
            $table->boolean('zerotime_deployment')->default(0);
            $table->string('automatic_deployment_id')->nullable();
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
        Schema::drop('sites');
    }
}
