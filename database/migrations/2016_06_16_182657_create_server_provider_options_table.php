<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerProviderOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_provider_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_provider_id');
            $table->string('memory');
            $table->integer('cpus');
            $table->integer('space');
            $table->float('priceHourly');
            $table->float('priceMonthly');
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
        Schema::drop('server_provider_options');
    }
}
