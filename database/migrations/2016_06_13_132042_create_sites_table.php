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
            $table->string('name');
            $table->increments('id');
            $table->string('domain');
            $table->integer('user_id');
            $table->integer('pile_id');
            $table->json('server_features')->nullable();
            $table->string('branch')->nullable();
            $table->text('repository')->nullable();
            $table->string('framework')->nullable();
            $table->text('web_directory')->nullable();
            $table->boolean('wildcard_domain')->default(0);
            $table->boolean('zerotime_deployment')->default(0);
            $table->string('automatic_deployment_id')->nullable();
            $table->integer('user_repository_provider_id')->nullable();
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
        Schema::dropIfExists('sites');
    }
}
