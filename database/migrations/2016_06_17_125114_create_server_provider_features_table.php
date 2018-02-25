<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerProviderFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_provider_features', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('server_provider_id');
            $table->string('feature');
            $table->string('option');
            $table->boolean('default');
            $table->string('cost')->nullable();

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
        Schema::dropIfExists('server_provider_features');
    }
}
