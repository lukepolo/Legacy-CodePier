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
            $table->string('description')->nullable();
            $table->unsignedInteger('server_provider_id');
            $table->string('memory');
            $table->unsignedInteger('cpus');
            $table->unsignedInteger('space');
            $table->float('priceHourly');
            $table->float('priceMonthly');
            $table->string('external_id')->nullable();
            $table->json('meta')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('server_provider_id');
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
