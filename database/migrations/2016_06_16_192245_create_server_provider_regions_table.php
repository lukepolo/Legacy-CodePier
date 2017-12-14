<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerProviderRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_provider_regions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('provider_name');
            $table->unsignedInteger('server_provider_id');

            $table->timestamps();

            $table->index('server_provider_id');
            $table->unsignedInteger('region_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_provider_regions');
    }
}
