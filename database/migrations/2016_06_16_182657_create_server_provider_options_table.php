<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->index('server_provider_id');
            $table->boolean('oauth')->default(1);
            $table->boolean('secret_token')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_provider_options');
    }
}
