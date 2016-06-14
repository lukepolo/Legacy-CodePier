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
            $table->text('path');
            $table->string('domain');
            $table->integer('user_id');
            $table->integer('server_id');
            $table->text('repository')->nullable();
            $table->boolean('zerotime_deployment')->default(0);
            $table->boolean('wildcard_domain')->default(0);
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
