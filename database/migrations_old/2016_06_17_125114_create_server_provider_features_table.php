<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('server_provider_id');
            $table->string('feature');
            $table->string('option');
            $table->boolean('default');
            $table->string('cost')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('server_provider_features');
    }
}
