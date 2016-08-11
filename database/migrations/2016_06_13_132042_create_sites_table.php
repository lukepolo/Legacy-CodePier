<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('server_id');
            $table->string('branch')->nullable();
            $table->text('repository')->nullable();
            $table->text('web_directory')->nullable();
            $table->integer('user_repository_provider_id');
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
